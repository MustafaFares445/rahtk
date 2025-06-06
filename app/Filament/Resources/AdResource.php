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

class AdResource extends Resource
{
    protected static ?string $model = Ad::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Ad Information')
                    ->description('Basic information about the advertisement')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter ad title')
                                    ->columnSpanFull(),

                                DatePicker::make('start_date')
                                    ->required()
                                    ->label('Start Date')
                                    ->displayFormat('Y-m-d')
                                    ->placeholder('Select start date'),

                                DatePicker::make('end_date')
                                    ->required()
                                    ->label('End Date')
                                    ->displayFormat('Y-m-d')
                                    ->placeholder('Select end date')
                                    ->after('start_date'),
                            ]),
                    ]),

                Section::make('Media')
                    ->description('Upload images and media files for this ad')
                    ->schema([
                        FileUpload::make('images')
                            ->multiple()
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->directory('ads')
                            ->visibility('public')
                            ->maxFiles(5)
                            ->reorderable()
                            ->columnSpanFull()
                            ->helperText('Upload images for this ad. Supported formats: JPG, PNG, GIF'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images')
                    ->label('Image')
                    ->square()
                    ->stacked()
                    ->getStateUsing(function ($record) {
                        $media = $record->getFirstMedia('images');
                        return $media ? $media->getUrl() : null;
                    }),

                TextColumn::make('title')
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
                    ->date('Y-m-d')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('end_date')
                    ->date('Y-m-d')
                    ->sortable()
                    ->badge()
                    ->color(function ($record) {
                        return now()->isAfter($record->end_date) ? 'danger' : 'warning';
                    }),

                TextColumn::make('status')
                    ->getStateUsing(function ($record) {
                        $now = now();
                        if ($now->isBefore($record->start_date)) {
                            return 'Scheduled';
                        } elseif ($now->isAfter($record->end_date)) {
                            return 'Expired';
                        } else {
                            return 'Active';
                        }
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'Scheduled' => 'info',
                        'Expired' => 'danger',
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Active Ads')
                    ->query(fn (Builder $query): Builder => $query->where('start_date', '<=', now())->where('end_date', '>=', now())),

                Tables\Filters\Filter::make('scheduled')
                    ->label('Scheduled Ads')
                    ->query(fn (Builder $query): Builder => $query->where('start_date', '>', now())),

                Tables\Filters\Filter::make('expired')
                    ->label('Expired Ads')
                    ->query(fn (Builder $query): Builder => $query->where('end_date', '<', now())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAds::route('/'),
            'create' => Pages\CreateAd::route('/create'),
            'view' => Pages\ViewAd::route('/{record}'),
            'edit' => Pages\EditAd::route('/{record}/edit'),
        ];
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