<?php

namespace App\Filament\Resources\AdResource\Widgets;

use App\Models\Ad;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $now = now();

        $totalAds = Ad::count();
        $activeAds = Ad::where('start_date', '<=', $now)
                       ->where('end_date', '>=', $now)
                       ->count();
        $scheduledAds = Ad::where('start_date', '>', $now)->count();
        $expiredAds = Ad::where('end_date', '<', $now)->count();

        return [
            Stat::make('Total Ads', $totalAds)
                ->description('All advertisements')
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('primary'),

            Stat::make('Active Ads', $activeAds)
                ->description('Currently running')
                ->descriptionIcon('heroicon-m-play')
                ->color('success'),

            Stat::make('Scheduled Ads', $scheduledAds)
                ->description('Waiting to start')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            Stat::make('Expired Ads', $expiredAds)
                ->description('Finished campaigns')
                ->descriptionIcon('heroicon-m-stop')
                ->color('danger'),
        ];
    }
}