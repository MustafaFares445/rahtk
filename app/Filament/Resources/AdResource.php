<?php

namespace App\Filament\Resources;

use App\Models\Ad;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AdResource\Pages;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class AdResource extends Resource
{
    protected static ?string $model = Ad::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'إعلانات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('معلومات الإعلان')
                    ->description('المعلومات الأساسية عن الإعلان')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('عنوان الإعلان')
                                    ->placeholder('أدخل عنوان الإعلان')
                                    ->columnSpanFull(),

                                DatePicker::make('start_date')
                                    ->required()
                                    ->label('تاريخ البدء')
                                    ->displayFormat('Y-m-d')
                                    ->placeholder('اختر تاريخ البدء'),

                                DatePicker::make('end_date')
                                    ->required()
                                    ->label('تاريخ الانتهاء')
                                    ->displayFormat('Y-m-d')
                                    ->placeholder('اختر تاريخ الانتهاء')
                                    ->after('start_date'),

                                SpatieMediaLibraryFileUpload::make('image')
                                    ->collection('images')
                                    ->image()
                                    ->imageEditor()
                                    ->label('صورة الإعلان')
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->columnSpanFull()
                                    ->helperText('قم بتحميل صورة للإعلان. الصيغ المدعومة: JPG, PNG, GIF'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                ->label('')
                ->size(40)
                ->circular()
                ->getStateUsing(fn($record) => $record->getFirstMediaUrl('images')),

                TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('start_date')
                    ->label('تاريخ البدء')
                    ->date('Y-m-d')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('end_date')
                    ->label('تاريخ الانتهاء')
                    ->date('Y-m-d')
                    ->sortable()
                    ->badge()
                    ->color(function ($record) {
                        return now()->isAfter($record->end_date) ? 'danger' : 'warning';
                    }),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->getStateUsing(function ($record) {
                        $now = now();
                        if ($now->isBefore($record->start_date)) {
                            return 'مجدول';
                        } elseif ($now->isAfter($record->end_date)) {
                            return 'منتهي';
                        } else {
                            return 'نشط';
                        }
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'نشط' => 'success',
                        'مجدول' => 'info',
                        'منتهي' => 'danger',
                    }),

                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('الإعلانات النشطة')
                    ->query(fn (Builder $query): Builder => $query->where('start_date', '<=', now())->where('end_date', '>=', now())),

                Tables\Filters\Filter::make('scheduled')
                    ->label('الإعلانات المجدولة')
                    ->query(fn (Builder $query): Builder => $query->where('start_date', '>', now())),

                Tables\Filters\Filter::make('expired')
                    ->label('الإعلانات المنتهية')
                    ->query(fn (Builder $query): Builder => $query->where('end_date', '<', now())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'media' => function ($query) {
                    $query->where('collection_name', 'images')->limit(1);
                }
            ]));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAds::route('/'),
            'create' => Pages\CreateAd::route('/create'),
            'edit' => Pages\EditAd::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['media']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Start Date' => $record->start_date?->format('Y-m-d'),
            'End Date' => $record->end_date?->format('Y-m-d'),
        ];
    }
}
