<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ProductGrowthChart;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\MostViewedProductChart;
use App\Filament\Widgets\ProductTypeDistributionChart;
use App\Filament\Resources\NoResource\Widgets\ProductOverviewWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.dashboard';

    public function getWidgets(): array
    {
        return [
            ProductOverviewWidget::class,
            ProductTypeDistributionChart::class,
            MostViewedProductChart::class,
            ProductGrowthChart::class,
        ];
    }
}
