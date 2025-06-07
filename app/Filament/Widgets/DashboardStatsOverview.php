<?php

namespace App\Filament\Widgets;

use App\Models\Ad;
use App\Models\School;
use App\Models\Product;
use App\Models\Teacher;
use Illuminate\Support\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Widgets\ProductTypeDistributionChart;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Resources\NoResource\Widgets\ProductOverviewWidget;

class DashboardStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Get the current date to find active ads
        $now = Carbon::now();

        return [
            Stat::make('Total Products', Product::count())
                ->description('Total number of all products')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),

            Stat::make('Active Ads', Ad::where('start_date', '<=', $now)->where('end_date', '>=', $now)->count())
                ->description('Advertisements currently running')
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('info'),

            Stat::make('Total Schools', School::count())
                ->description('Total number of registered schools')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning'),

            Stat::make('Total Teachers', Teacher::count())
                ->description('Total number of teachers across all schools')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}