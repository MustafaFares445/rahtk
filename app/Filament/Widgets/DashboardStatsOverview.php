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
        // الحصول على التاريخ الحالي للعثور على الإعلانات النشطة
        $now = Carbon::now();

        return [
            Stat::make('إجمالي المنتجات', Product::count())
                ->description('إجمالي عدد جميع المنتجات')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),

            Stat::make('الإعلانات النشطة', Ad::where('start_date', '<=', $now)->where('end_date', '>=', $now)->count())
                ->description('الإعلانات التي تعمل حاليًا')
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('info'),

            Stat::make('إجمالي المدارس', School::count())
                ->description('إجمالي عدد المدارس المسجلة')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning'),

            Stat::make('إجمالي المعلمين', Teacher::count())
                ->description('إجمالي عدد المعلمين في جميع المدارس')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}