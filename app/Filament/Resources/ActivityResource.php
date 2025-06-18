<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Activity;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\DateFilter;
use Filament\Tables\Actions\ActionGroup;
use App\Filament\Resources\ActivityResource\Pages;
use Filament\Notifications\Notification;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'المسابقات';
    protected static ?string $modelLabel = 'مسابقة';
    protected static ?string $pluralModelLabel = 'المسابقات';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                ->label('عنوان المسابقة')
                ->required()
                ->maxLength(255)
                ->placeholder('مثال: مسابقة السيارات السنوية')
                ->helperText('اختر عنواناً واضحاً ومميزاً للمسابقة')
                ->columnSpanFull(),

            Forms\Components\TextInput::make('url')
                ->label('رابط المسابقة')
                ->required()
                ->url()
                ->maxLength(255)
                ->placeholder('https://example.com/competition')
                ->helperText('أدخل الرابط الكامل للمسابقة')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان المسابقة')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('bold')
                    ->icon('heroicon-o-trophy'),

                Tables\Columns\TextColumn::make('url')
                    ->label('الرابط')
                    ->url(fn ($record) => $record->url)
                    ->openUrlInNewTab()
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->url)
                    ->icon('heroicon-o-link')
                    ->color('primary'),


                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock'),

            ])
            ->filters([

            ])
            ->actions([

            Tables\Actions\EditAction::make()
                ->label('تعديل')
                ->icon('heroicon-o-pencil')
                ->color('warning'),

            Tables\Actions\DeleteAction::make()
                ->label('حذف')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('حذف المسابقة')
                ->modalDescription('هل أنت متأكد من حذف هذه المسابقة؟ لا يمكن التراجع عن هذا الإجراء.')
                ->modalSubmitActionLabel('نعم، احذف')
                ->modalCancelActionLabel('إلغاء'),

            Tables\Actions\Action::make('visit')
                ->label('زيارة الرابط')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn ($record) => $record->url)
                ->openUrlInNewTab()
                ->color('success'),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف المحدد')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->modalHeading('حذف المسابقات المحددة')
                        ->modalDescription('هل أنت متأكد من حذف المسابقات المحددة؟ لا يمكن التراجع عن هذا الإجراء.')
                        ->modalSubmitActionLabel('نعم، احذف الكل')
                        ->modalCancelActionLabel('إلغاء'),

                ]),
            ])
            ->emptyStateHeading('لا توجد مسابقات')
            ->emptyStateDescription('ابدأ بإضافة أول مسابقة باستخدام الزر أدناه')
            ->emptyStateIcon('heroicon-o-trophy')
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageActivities::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }
}