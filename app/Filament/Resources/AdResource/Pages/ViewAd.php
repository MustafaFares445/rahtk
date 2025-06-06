<?php

namespace App\Filament\Resources\AdResource\Pages;

use App\Filament\Resources\AdResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Grid;

class ViewAd extends ViewRecord
{
    protected static string $resource = AdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Ad Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Title')
                                    ->columnSpanFull(),

                                TextEntry::make('start_date')
                                    ->label('Start Date')
                                    ->date('Y-m-d')
                                    ->badge()
                                    ->color('success'),

                                TextEntry::make('end_date')
                                    ->label('End Date')
                                    ->date('Y-m-d')
                                    ->badge()
                                    ->color(function ($record) {
                                        return now()->isAfter($record->end_date) ? 'danger' : 'warning';
                                    }),

                                TextEntry::make('status')
                                    ->label('Status')
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
                            ]),
                    ]),

                Section::make('Media')
                    ->schema([
                        ImageEntry::make('images')
                            ->label('Ad Images')
                            ->getStateUsing(function ($record) {
                                return $record->getMedia('images')->map(function ($media) {
                                    return $media->getUrl();
                                })->toArray();
                            })
                            ->size(200)
                            ->square(false)
                            ->columnSpanFull(),
                    ]),

                Section::make('Timestamps')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label('Updated At')
                                    ->dateTime(),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}