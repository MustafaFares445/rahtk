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
use App\Filament\Resources\ProductResource\Pages;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    private static array $typeFieldMappings = [
        'estate' => 'getEstateFields',
        'school' => 'getSchoolFields',
        'car' => 'getCarFields',
        'electronic' => 'getElectronicFields',
        'farm' => 'getFarmFields',
        'building' => 'getBuildingFields',
    ];

    // Badge colors for product types
    private static array $typeBadgeColors = [
        'estate' => 'primary',
        'school' => 'success',
        'car' => 'warning',
        'electronic' => 'danger',
        'farm' => 'info',
        'building' => 'gray',
    ];

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::getTableColumns())
            ->filters(self::getTableFilters())
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'media' => function ($query) {
                    $query->where('collection_name', 'images')->limit(1);
                }
            ]));
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            ...self::getCommonFields(),
            ...self::getMediaFields(),
            ...self::getTypeSpecificFields(),
        ]);
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

    private static function getCommonFields(): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('address')
                ->required()
                ->maxLength(255),

            Forms\Components\Toggle::make('is_urgent')
                ->default(false),

            Forms\Components\Textarea::make('description')
                ->required()
                ->columnSpanFull(),

            Forms\Components\TextInput::make('price')
                ->required()
                ->numeric(),

            Forms\Components\TextInput::make('discount')
                ->numeric(),


            Forms\Components\Select::make('type')
                ->options(ProductTypes::class)
                ->required()
                ->enum(ProductTypes::class)
                ->live()
                ->afterStateUpdated(fn($state, Forms\Set $set) => $set('relationship_data', null))
                ->hint(self::getLoadingIndicator()),
        ];
    }

    private static function getMediaFields(): array
    {
        return [
            Forms\Components\Section::make('Media')
                ->schema([
                    // Existing Images Display
                    Forms\Components\Placeholder::make('existing_images')
                        ->label('Current Images')
                        ->visible(fn ($record) => $record && $record->getMedia('images')->count() > 0),

                    // New Images Upload
                    Forms\Components\FileUpload::make('new_images')
                        ->label('Upload New Images')
                        ->image()
                        ->multiple()
                        ->directory('products')
                        ->preserveFilenames()
                        ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'])
                        ->maxSize(10240) // 10MB max per image
                        ->maxFiles(10)
                        ->reorderable(),

                    // Existing Videos Display
                    Forms\Components\Placeholder::make('existing_videos')
                        ->label('Current Videos')
                        ->visible(fn ($record) => $record && $record->getMedia('videos')->count() > 0),

                    // New Video Upload
                    Forms\Components\FileUpload::make('new_video')
                        ->label('Upload New Video')
                        ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv', 'video/webm'])
                        ->directory('products/videos')
                        ->preserveFilenames()
                        ->maxSize(102400), // 100MB max
                ])
                ->columnSpanFull(),
        ];
    }

    private static function getLoadingIndicator(): HtmlString
    {
        return new HtmlString(Blade::render(
            '<x-filament::loading-indicator class="h-5 w-5 text-gray-500 inline-block" wire:loading wire:target="data.type" />'
        ));
    }

    private static function getTypeSpecificFields(): array
    {
        return [
            Forms\Components\Group::make()
                ->schema([
                    self::getLoadingPlaceholder(),
                    Forms\Components\Group::make()
                        ->schema(fn(Forms\Get $get) => self::getFieldsForType($get('type')))
                        ->hidden(fn(Forms\Get $get) => !$get('type')),
                ])
        ];
    }

    private static function getLoadingPlaceholder(): Forms\Components\Placeholder
    {
        return Forms\Components\Placeholder::make('loading')
            ->hidden()
            ->content(new HtmlString(
                '<div wire:loading wire:target="data.type" class="flex items-center space-x-2">
                    <x-filament::loading-indicator class="h-5 w-5 text-gray-600" />
                    <span>Loading fields…</span>
                 </div>'
            ));
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
            Forms\Components\Section::make('Estate Attributes')
            ->schema([
                Forms\Components\TextInput::make('rooms')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('area')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('floors_number')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_furnished')
                    ->required(),
                Forms\Components\TextInput::make('floor')
                    ->required()
                    ->numeric(),
            ])

        ];
    }

    private static function getSchoolFields(): array
    {
       return [
            Forms\Components\Section::make('School Attributes')
                ->schema([
                    Forms\Components\TextInput::make('quate')
                        ->nullable()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('working_duration')
                        ->nullable()
                        ->maxLength(255),
                    Forms\Components\DatePicker::make('founding_date')
                        ->required()
                        ->format('Y-m-d')
                        ->minDate(now()->subYears(100))
                        ->maxDate(now()),
                    Forms\Components\TextInput::make('address')
                        ->nullable()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('manager')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('manager_description')
                        ->required()
                        ->columnSpanFull(),
            ])
        ];
    }

    private static function getCarFields(): array
    {
        return [
            Forms\Components\Section::make('Car Attributes')
                ->schema([
                    Forms\Components\TextInput::make('model')
                        ->required()
                        ->maxLength(255),
                    self::getYearField(),
                    Forms\Components\TextInput::make('kilo')
                        ->required()
                        ->numeric(),
                ])
        ];
    }

    private static function getElectronicFields(): array
    {
        return [
            Section::make('Electronic Attributes')
                ->schema([
                    Forms\Components\TextInput::make('model')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('brand')
                    ->required()
                    ->maxLength(255),

                self::getYearField(),
            ])
        ];
    }

    private static function getFarmFields(): array
    {
        return [
           Section::make('Farm Attributes')
            ->schema([
                Forms\Components\TextInput::make('type')
                ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('bedrooms')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('bathrooms')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('floors_number')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('size')
                    ->required()
                    ->numeric(),
            ])
        ];
    }

    private static function getBuildingFields(): array
    {
        return [
            Forms\Components\Section::make('Building Attributes')
                ->schema([
                    Forms\Components\TextInput::make('type')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('brand')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('options')
                        ->required()
                        ->maxLength(255),
                ]),
        ];
    }

    // Reusable components
    private static function getYearField(): Forms\Components\DatePicker
    {
        return Forms\Components\DatePicker::make('year')
            ->required()
            ->format('Y')
            ->minDate(now()->subYears(100))
            ->maxDate(now());
    }

    private static function getTableColumns(): array
    {
        return [
            Tables\Columns\ImageColumn::make('images')
                ->label('Primary Image')
                ->getStateUsing(fn($record) => $record->getFirstMediaUrl('images')),

            Tables\Columns\TextColumn::make('title')
                ->searchable(),

            Tables\Columns\TextColumn::make('type')
                ->searchable()
                ->badge()
                ->color(fn(string $state): string =>
                    self::$typeBadgeColors[$state] ?? 'secondary'
                ),

            Tables\Columns\TextColumn::make('price')
                ->numeric()
                ->sortable(),

            Tables\Columns\TextColumn::make('discount')
                ->numeric()
                ->sortable(),

            Tables\Columns\TextColumn::make('address')
                ->searchable(),

            Tables\Columns\IconColumn::make('is_urgent')
                ->boolean(),

            Tables\Columns\TextColumn::make('view')
                ->numeric()
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    private static function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('type')
                ->options(ProductTypes::class)
                ->label('Product Type'),

            Tables\Filters\Filter::make('is_urgent')
                ->label('Urgent Products Only')
                ->query(fn(Builder $query) => $query->where('is_urgent', true)),

            Tables\Filters\Filter::make('price_range')
                ->form([
                    Forms\Components\TextInput::make('min_price')
                        ->numeric()
                        ->placeholder('Min Price'),
                    Forms\Components\TextInput::make('max_price')
                        ->numeric()
                        ->placeholder('Max Price'),
                ])
                ->query(function (Builder $query, array $data) {
                    return $query
                        ->when(
                            $data['min_price'],
                            fn(Builder $query, $minPrice) => $query->where('price', '>=', $minPrice)
                        )
                        ->when(
                            $data['max_price'],
                            fn(Builder $query, $maxPrice) => $query->where('price', '<=', $maxPrice)
                        );
                }),
        ];
    }

}