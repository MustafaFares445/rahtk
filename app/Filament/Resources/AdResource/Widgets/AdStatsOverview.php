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
            Stat::make('إجمالي الإعلانات', $totalAds)
                ->description('جميع الإعلانات')
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('primary'),

            Stat::make('الإعلانات النشطة', $activeAds)
                ->description('تعمل حاليًا')
                ->descriptionIcon('heroicon-m-play')
                ->color('success'),

            Stat::make('الإعلانات المجدولة', $scheduledAds)
                ->description('في انتظار البدء')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            Stat::make('الإعلانات المنتهية', $expiredAds)
                ->description('الحملات المنتهية')
                ->descriptionIcon('heroicon-m-stop')
                ->color('danger'),
        ];
    }
}