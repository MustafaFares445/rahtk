<?php

namespace App\Filament\Widgets;

use App\Services\Filament\ProductStatics;
use Filament\Widgets\ChartWidget;

/**
 * Product Type Distribution Chart
 */
class ProductTypeDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Product Count by Type';

    protected int | string | array $columnSpan = 2;

    protected function getData(): array
    {
        return ProductStatics::getTypeDistributionChartData();
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
            'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => false,
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'display' => false,
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
            'responsive' => true,
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '1m';
    }
}