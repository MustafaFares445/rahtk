<?php

namespace App\Filament\Resources;

use App\Enums\ProductTypes;
use App\Models\Product;
use App\Filament\Resources\ProductResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Products';
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
        return Section::make('Product Details')
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->placeholder('Enter product title'),

                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->rows(3)
                    ->placeholder('Detailed product description'),

                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->step(0.01),

                Forms\Components\TextInput::make('discount')
                    ->numeric()
                    ->prefix('%')
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.1),

                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->placeholder('Product location address'),

                Forms\Components\Select::make('type')
                    ->options(
                        collect(ProductTypes::cases())
                            ->filter(fn($type) => $type->value !== 'school')
                            ->mapWithKeys(fn($type) => [$type->value => ucfirst($type->value)])
                            ->toArray()
                    )
                    ->required()
                    ->enum(ProductTypes::class)
                    ->live()
                    ->native(false)
                    ->afterStateUpdated(fn($state, Forms\Set $set) => $set('relationship_data', null))
                    ->hint(self::getLoadingIndicator()),

                Forms\Components\Toggle::make('is_urgent')
                    ->label('Mark as Urgent')
                    ->default(false)
                    ->inline(false)
                    ->helperText('Highlight this product as urgent'),
            ])
            ->columns(2);
    }

    private static function getMediaSection(): Section
    {
        return Section::make('Media & Attachments')
            ->schema([
                Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                    ->collection('images')
                    ->image()
                    ->multiple()
                    ->preserveFilenames()
                    ->required()
                    ->label('Product Images')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->helperText('Upload product images (JPEG, PNG, GIF, WebP)')
                    ->reorderable()
                    ->imagePreviewHeight('150')
                    ->columnSpanFull()
                    ->downloadable()
                    ->openable(),

                Forms\Components\SpatieMediaLibraryFileUpload::make('video')
                    ->collection('videos')
                    ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv'])
                    ->preserveFilenames()
                    ->label('Product Video')
                    ->helperText('Optional product video (MP4, MOV, AVI, WMV)')
                    ->columnSpanFull()
                    ->downloadable(),
            ]);
    }

    /* ==================== TYPE-SPECIFIC FIELDS ==================== */

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
            Section::make('Real Estate Details')
                ->schema([
                    Forms\Components\TextInput::make('rooms')
                        ->numeric()
                        ->minValue(1)
                        ->step(1)
                        ->label('Rooms'),

                    Forms\Components\TextInput::make('area')
                        ->numeric()
                        ->minValue(1)
                        ->suffix('sq ft')
                        ->label('Area'),

                    Forms\Components\TextInput::make('floors_number')
                        ->numeric()
                        ->minValue(1)
                        ->step(1)
                        ->label('Floors'),

                    Forms\Components\Toggle::make('is_furnished')
                        ->inline(false)
                        ->label('Furnished'),

                    Forms\Components\TextInput::make('floor')
                        ->numeric()
                        ->minValue(0)
                        ->step(1)
                        ->label('Floor Number'),
                ])
                ->columns(2)
        ];
    }

    private static function getCarFields(): array
    {
        return [
            Section::make('Vehicle Details')
                ->schema([
                    Forms\Components\TextInput::make('model')
                        ->maxLength(255)
                        ->placeholder('Model name')
                        ->label('Model'),

                    self::getYearField(),

                    Forms\Components\TextInput::make('kilo')
                        ->numeric()
                        ->minValue(0)
                        ->suffix('km')
                        ->label('Kilometers'),
                ])
                ->columns(2)
        ];
    }

    private static function getElectronicFields(): array
    {
        return [
            Section::make('Electronic Specifications')
                ->schema([
                    Forms\Components\TextInput::make('model')
                        ->maxLength(255)
                        ->placeholder('Model number')
                        ->label('Model'),

                    Forms\Components\TextInput::make('brand')
                        ->maxLength(255)
                        ->placeholder('Brand name')
                        ->label('Brand'),

                    self::getYearField(),
                ])
                ->columns(2)
        ];
    }

    private static function getFarmFields(): array
    {
        return [
            Section::make('Farm Property Details')
                ->schema([
                    Forms\Components\TextInput::make('type')
                        ->maxLength(255)
                        ->placeholder('Farm type')
                        ->label('Farm Type'),

                    Forms\Components\TextInput::make('bedrooms')
                        ->numeric()
                        ->minValue(0)
                        ->step(1)
                        ->label('Bedrooms'),

                    Forms\Components\TextInput::make('bathrooms')
                        ->numeric()
                        ->minValue(0)
                        ->step(1)
                        ->label('Bathrooms'),

                    Forms\Components\TextInput::make('floors_number')
                        ->numeric()
                        ->minValue(1)
                        ->step(1)
                        ->label('Floors'),

                    Forms\Components\TextInput::make('size')
                        ->numeric()
                        ->minValue(1)
                        ->suffix('acres')
                        ->label('Size'),
                ])
                ->columns(2)
        ];
    }

    private static function getBuildingFields(): array
    {
        return [
            Section::make('Building Specifications')
                ->schema([
                    Forms\Components\TextInput::make('type')
                        ->maxLength(255)
                        ->placeholder('Building type')
                        ->label('Building Type'),

                    Forms\Components\TextInput::make('brand')
                        ->maxLength(255)
                        ->placeholder('Construction brand')
                        ->label('Brand'),

                    Forms\Components\TextInput::make('options')
                        ->maxLength(255)
                        ->placeholder('Special features')
                        ->label('Features'),
                ])
                ->columns(2)
        ];
    }

    /* ==================== REUSABLE COMPONENTS ==================== */

    private static function getYearField(): Forms\Components\DatePicker
    {
        return Forms\Components\DatePicker::make('year')
            ->required()
            ->format('Y')
            ->displayFormat('Y')
            ->native(false)
            ->minDate(now()->subYears(100))
            ->maxDate(now())
            ->columnSpan(1)
            ->label('Year');
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
                    <span>Loading product specifications...</span>
                 </div>'
            ));
    }

    /* ==================== TABLE CONFIGURATION ==================== */

    private static function getTableColumns(): array
    {
        return [
            Tables\Columns\ImageColumn::make('images')
                ->label('')
                ->size(40)
                ->circular()
                ->getStateUsing(fn($record) => $record->getFirstMediaUrl('images')),

            Tables\Columns\TextColumn::make('title')
                ->searchable()
                ->sortable()
                ->weight('medium')
                ->description(fn($record) => \Illuminate\Support\Str::limit($record->description, 30))
                ->wrap(),

            Tables\Columns\TextColumn::make('type')
                ->badge()
                ->color(fn(string $state): string => self::$typeBadgeColors[$state] ?? 'secondary')
                ->formatStateUsing(fn($state) => ucfirst($state))
                ->sortable(),

            Tables\Columns\TextColumn::make('price')
                ->money('USD')
                ->sortable()
                ->color(fn($record) => $record->discount ? 'success' : null)
                ->description(fn($record) => $record->discount ? 'Discounted from $'.number_format($record->price / (1 - $record->discount / 100), 2) : null),

            Tables\Columns\IconColumn::make('is_urgent')
                ->label('Urgent')
                ->boolean()
                ->trueIcon('heroicon-o-bolt')
                ->trueColor('warning')
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Posted')
                ->sortable()
                ->icon('heroicon-o-calendar')
                ->color('success')
                ->dateTime('M d, Y'),

            Tables\Columns\TextColumn::make('view')
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
                        ->mapWithKeys(fn($type) => [$type->value => ucfirst($type->value)])
                        ->toArray()
                )
                ->label('Type')
                ->native(false)
                ->multiple(),

            Tables\Filters\TernaryFilter::make('is_urgent')
                ->label('Urgent Products')
                ->placeholder('All products')
                ->trueLabel('Only urgent')
                ->falseLabel('Not urgent'),

            Tables\Filters\Filter::make('price_range')
                ->form([
                    Forms\Components\TextInput::make('min_price')
                        ->numeric()
                        ->prefix('$')
                        ->placeholder('Min'),
                    Forms\Components\TextInput::make('max_price')
                        ->numeric()
                        ->prefix('$')
                        ->placeholder('Max'),
                ])
                ->query(function (Builder $query, array $data) {
                    $query
                        ->when($data['min_price'], fn($q, $min) => $q->where('price', '>=', $min))
                        ->when($data['max_price'], fn($q, $max) => $q->where('price', '<=', $max));
                })
                ->indicateUsing(function (array $data) {
                    $indicators = [];
                    if ($data['min_price']) {
                        $indicators[] = 'Min price: $'.number_format($data['min_price'], 2);
                    }
                    if ($data['max_price']) {
                        $indicators[] = 'Max price: $'.number_format($data['max_price'], 2);
                    }
                    return $indicators;
                }),
        ];
    }
}