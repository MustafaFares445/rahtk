<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ProductGrowthChart;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\MostViewedProductChart;
use App\Filament\Widgets\ProductTypeDistributionChart;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.dashboard';

    // protected static ?string $navigationLabel = ''

    public function getWidgets(): array
    {
return [
            ProductTypeDistributionChart::class,
            MostViewedProductChart::class,
            ProductGrowthChart::class,
        ];
    }
}
