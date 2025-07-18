<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\ProductTypes;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Log as FacadesLog;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'المنتجات';
    protected static ?string $modelLabel = 'Product';
    protected static ?string $recordTitleAttribute = 'title';

    private static array $typeFieldMappings = [
        'estate' => 'getEstateFields',
        'car' => 'getCarFields',
        'electronic' => 'getElectronicFields',
        'farm' => 'getFarmFields',
        'building' => 'getBuildingFields',
    ];

    private static array $typeBadgeColors = [
        'estate' => 'primary',
        'car' => 'warning',
        'electronic' => 'danger',
        'farm' => 'info',
        'building' => 'gray',
    ];

    public static function form(Form $form): Form
    {
        return $form->schema([
            self::buildMainFormSections(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::getTableColumns())
            ->filters(self::getTableFilters())
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('type' , '!=' , ProductTypes::SCHOOL->value)->with([
                'media' => fn ($query) => $query->where('collection_name', 'images')->limit(1)
            ]))
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function handleRelationshipData(Product $product, array $data): void
    {
        $type = $data['type'];
        $relationshipData = $data['relationship_data'] ?? [];

        if (isset(self::$typeFieldMappings[$type])) {
            $product->$type()->updateOrCreate([], $relationshipData);
        }
    }

    /* ==================== FORM BUILDERS ==================== */

    private static function buildMainFormSections(): Forms\Components\Grid
    {
        return Forms\Components\Grid::make()
            ->schema([
                // Left column - Product Details and Type-Specific Fields
                Forms\Components\Section::make()
                    ->schema([
                        self::getProductDetailsSection(),
                        self::getTypeSpecificFields(),
                    ])
                    ->columnSpan(['lg' => 2]),

                // Right column - Media Section
                self::getMediaSection()
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns([
                'default' => 1,
                'lg' => 3,
            ]);
    }

    private static function getProductDetailsSection(): Section
    {
        return Section::make('تفاصيل المنتج')
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('اسم المنتج')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->placeholder('أدخل عنوان المنتج'),

                Forms\Components\Textarea::make('description')
                    ->label('الوصف')
                    ->required()
                    ->columnSpanFull()
                    ->rows(3)
                    ->placeholder('وصف مفصل للمنتج'),

                Forms\Components\TextInput::make('price')
                    ->label('السعر')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->step(0.01),

                Forms\Components\TextInput::make('discount')
                    ->label('الخصم')
                    ->numeric()
                    ->prefix('%')
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.1),

                Forms\Components\TextInput::make('address')
                    ->label('العنوان')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->placeholder('عنوان موقع المنتج'),

                Forms\Components\Select::make('type')
                ->label('النوع')
                ->options(
                    collect(ProductTypes::cases())
                        ->filter(fn($type) => $type->value !== 'school')
                        ->mapWithKeys(fn($type) => [
                            $type->value => match ($type->value) {
                                'estate' => 'عقار',
                                'farm' => 'مزرعة',
                                'car' => 'سيارة',
                                'electronic' => 'إلكترونيات',
                                'building' => 'مواد بناء',
                                default => ucfirst($type->value),
                            }
                        ])
                        ->toArray()
                )
                ->required()
                ->enum(ProductTypes::class)
                ->live()
                ->native(false)
                ->afterStateUpdated(fn($state, Forms\Set $set) => $set('relationship_data', null))
                ->placeholder('أختر نوع المنتج')
                ->hint(self::getLoadingIndicator()),

                Forms\Components\Toggle::make('is_urgent')
                    ->label('وضع علامة مستعجل')
                    ->default(false)
                    ->inline(false),
            ])
            ->columns(2);
    }

    private static function getMediaSection(): Section
    {
        return Section::make('الوسائط والمرفقات')
            ->schema([
                Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                    ->collection('images')
                    ->image()
                    ->multiple()
                    ->preserveFilenames()
                    ->required()
                    ->label('صور المنتج')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->helperText('رفع صور المنتج (JPEG, PNG, GIF, WebP)')
                    ->reorderable()
                    ->imagePreviewHeight('150')
                    ->columnSpanFull()
                    ->downloadable()
                    ->openable(),

                Forms\Components\SpatieMediaLibraryFileUpload::make('videos')
                    ->collection('videos')
                    ->multiple()
                    ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv'])
                    ->preserveFilenames()
                    ->label('فيديوهات المنتج')
                    ->helperText('فيديوهات المنتج (اختياري) (MP4, MOV, AVI, WMV)')
                    ->columnSpanFull()
                    ->downloadable(),
            ]);
    }

    /* ==================== حقول خاصة بالنوع ==================== */

    private static function getTypeSpecificFields(): Forms\Components\Group
    {
        return Forms\Components\Group::make()
            ->schema([
                self::getLoadingPlaceholder(),
                Forms\Components\Group::make()
                    ->schema(fn(Forms\Get $get) => self::getFieldsForType($get('type')))
                    ->hidden(fn(Forms\Get $get) => !$get('type')),
            ])
            ->columnSpanFull();
    }

    private static function getFieldsForType(?string $type): array
    {
        if (!$type || !isset(self::$typeFieldMappings[$type])) {
            return [];
        }

        $methodName = self::$typeFieldMappings[$type];
        return [
            Forms\Components\Group::make()
                ->relationship($type)
                ->schema(self::$methodName())
        ];
    }

    private static function getEstateFields(): array
    {
        return [
            Section::make('تفاصيل العقار')
                ->schema([
                    Forms\Components\TextInput::make('rooms')
                        ->numeric()
                        ->minValue(1)
                        ->step(1)
                        ->label('الغرف')
                        ->required(),

                    Forms\Components\TextInput::make('area')
                        ->numeric()
                        ->minValue(1)
                        ->suffix('قدم مربع')
                        ->label('المساحة')
                        ->required(),

                    Forms\Components\TextInput::make('floors_number')
                        ->numeric()
                        ->minValue(1)
                        ->step(1)
                        ->label('الطوابق')
                        ->required(),

                    Forms\Components\TextInput::make('floor')
                        ->numeric()
                        ->minValue(0)
                        ->step(1)
                        ->label('رقم الطابق')
                        ->required(),

                    Forms\Components\Select::make('type')
                        ->options([
                            'بيع' => 'بيع',
                            'أجار' => 'أجار',
                            'بيع/أجار' => 'بيع/أجار'
                        ])
                        ->placeholder('نوع العقار بيع أو عاجل')
                        ->required(),
                ])
                ->columns(2)
        ];
    }

    private static function getCarFields(): array
    {
        return [
            Section::make('تفاصيل المركبة')
                ->schema([
                    Forms\Components\TextInput::make('model')
                        ->maxLength(255)
                        ->placeholder('اسم الموديل')
                        ->label('الموديل')
                        ->required(),

                    self::getYearField()
                ])
                ->columns(2)
        ];
    }

    private static function getElectronicFields(): array
    {
        return [
            Section::make('مواصفات الإلكترونيات')
                ->schema([
                    Forms\Components\TextInput::make('model')
                        ->maxLength(255)
                        ->placeholder('رقم الموديل')
                        ->label('الموديل')
                        ->required(),

                    Forms\Components\TextInput::make('brand')
                        ->maxLength(255)
                        ->placeholder('اسم الماركة')
                        ->label('الماركة')
                        ->required(),

                    self::getYearField(),
                ])
                ->columns(2)
        ];
    }

    private static function getFarmFields(): array
    {
        return [
            Section::make('تفاصيل المزرعة')
                ->schema([
                    Forms\Components\TextInput::make('bedrooms')
                        ->numeric()
                        ->minValue(0)
                        ->step(1)
                        ->label('غرف النوم')
                        ->required(),

                    Forms\Components\TextInput::make('bathrooms')
                        ->numeric()
                        ->minValue(0)
                        ->step(1)
                        ->label('الحمامات')
                        ->required(),

                    Forms\Components\TextInput::make('floors_number')
                        ->numeric()
                        ->minValue(1)
                        ->step(1)
                        ->label('الطوابق')
                        ->required(),

                    Forms\Components\TextInput::make('size')
                        ->numeric()
                        ->minValue(1)
                        ->suffix('فدان')
                        ->label('المساحة')
                        ->required(),
                ])
                ->columns(2)
        ];
    }

    private static function getBuildingFields(): array
    {
        return [
            Section::make('مواصفات المبنى')
                ->schema([
                    Forms\Components\TextInput::make('type')
                        ->maxLength(255)
                        ->placeholder('نوع مادة البناء')
                        ->label('نوع  مادة البناء')
                        ->required(),

                    Forms\Components\TextInput::make('brand')
                        ->maxLength(255)
                        ->placeholder('ماركة مادة البناء')
                        ->label('الماركة')
                        ->required(),

                    Forms\Components\TextInput::make('options')
                        ->maxLength(255)
                        ->placeholder('ميزات خاصة')
                        ->label('الميزات')
                        ->required(),
                ])
                ->columns(2)
        ];
    }

    /* ==================== مكونات قابلة لإعادة الاستخدام ==================== */

    private static function getYearField(): Forms\Components\TextInput
    {
        return  Forms\Components\TextInput::make('year')
            ->label('السنة')
            ->numeric()
            ->minValue(1900)
            ->maxValue(now()->year)
            ->placeholder('أدخل سنة بين عامي 1900 و السنة الحالية')
            ->extraInputAttributes(['dir' => 'rtl'])
            ->required();
    }

    private static function getLoadingIndicator(): HtmlString
    {
        return new HtmlString(Blade::render(
            '<x-filament::loading-indicator class="h-4 w-4 text-primary-500 inline-block" wire:loading wire:target="data.type" />'
        ));
    }

    private static function getLoadingPlaceholder(): Forms\Components\Placeholder
    {
        return Forms\Components\Placeholder::make('loading')
            ->hidden()
            ->content(new HtmlString(
                '<div wire:loading wire:target="data.type" class="flex items-center space-x-2 text-sm text-gray-600 p-2">
                    <x-filament::loading-indicator class="h-4 w-4" />
                    <span>جاري تحميل مواصفات المنتج...</span>
                 </div>'
            ));
    }

    /* ==================== تكوين الجدول ==================== */

    private static function getTableColumns(): array
    {
        return [
            Tables\Columns\ImageColumn::make('images')
                ->label('')
                ->size(40)
                ->circular()
                ->getStateUsing(fn($record) => $record->getFirstMediaUrl('images')),

            Tables\Columns\TextColumn::make('title')
                ->label('العنوان')
                ->searchable()
                ->sortable()
                ->weight('medium')
                ->description(fn($record) => \Illuminate\Support\Str::limit($record->description, 30))
                ->wrap(),

            Tables\Columns\TextColumn::make('type')
                ->label('النوع')
                ->badge()
                ->color(fn(string $state): string => self::$typeBadgeColors[$state] ?? 'secondary')
                ->formatStateUsing(fn($state) => match ($state) {
                    'estate' => 'عقار',
                    'farm' => 'مزرعة',
                    'car' => 'سيارة',
                    'electronic' => 'إلكترونيات',
                    'building' => 'مواد بناء',
                    default => ucfirst($state),
                })
                ->sortable(),

            Tables\Columns\TextColumn::make('price')
                ->label('السعر')
                ->money('USD')
                ->sortable()
                ->color(fn($record) => $record->discount ? 'success' : null)
                ->formatStateUsing(fn($state, $record) => $record->discount ? $state * (1 - $record->discount / 100) : $state),

            Tables\Columns\IconColumn::make('is_urgent')
                ->label('مستعجل')
                ->default(false)
                ->boolean()
                ->trueIcon('heroicon-o-bolt')
                ->trueColor('warning')
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('تاريخ النشر')
                ->sortable()
                ->icon('heroicon-o-calendar')
                ->color('success')
                ->dateTime('M d, Y'),

            Tables\Columns\TextColumn::make('view')
                ->label('المشاهدات')
                ->numeric()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    private static function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('type')
                ->options(
                    collect(ProductTypes::cases())
                        ->mapWithKeys(fn($type) => [
                            $type->value => match ($type->value) {
                                'estate' => 'عقار',
                                'farm' => 'مزرعة',
                                'car' => 'سيارة',
                                'electronic' => 'إلكترونيات',
                                'building' => 'مواد بناء',
                                default => ucfirst($type->value),
                            }
                        ])
                        ->toArray()
                )
                ->label('النوع')
                ->native(false)
                ->multiple(),

            Tables\Filters\TernaryFilter::make('is_urgent')
                ->label('المنتجات المستعجلة')
                ->placeholder('جميع المنتجات')
                ->trueLabel('المنتجات المستعجلة فقط')
                ->falseLabel('غير مستعجل'),

            Tables\Filters\Filter::make('price_range')
                ->form([
                    Forms\Components\TextInput::make('min_price')
                        ->numeric()
                        ->prefix('$')
                        ->placeholder('الحد الأدنى'),
                    Forms\Components\TextInput::make('max_price')
                        ->numeric()
                        ->prefix('$')
                        ->placeholder('الحد الأقصى'),
                ])
                ->query(function (Builder $query, array $data) {
                    $query
                        ->when($data['min_price'], fn($q, $min) => $q->where('price', '>=', $min))
                        ->when($data['max_price'], fn($q, $max) => $q->where('price', '<=', $max));
                })
                ->indicateUsing(function (array $data) {
                    $indicators = [];
                    if ($data['min_price']) {
                        $indicators[] = 'الحد الأدنى للسعر: $'.number_format($data['min_price'], 2);
                    }
                    if ($data['max_price']) {
                        $indicators[] = 'الحد الأقصى للسعر: $'.number_format($data['max_price'], 2);
                    }
                    return $indicators;
                })
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
