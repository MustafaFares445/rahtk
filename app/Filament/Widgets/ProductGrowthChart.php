<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Support\Colors\Color;
use App\Services\Filament\ProductStatics;

/**
 * Product Growth Trend Chart with Enhanced Styling
 */
class ProductGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Product Growth Trend (12 Months)';

    protected static ?string $description = 'Monthly product growth tracking over the past year';

    protected static ?int $sort = 2;

    protected static string $color = 'info';

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = ProductStatics::getGrowthChartData();

        // Enhance the data structure with styling
        return [
            'datasets' => [
                [
                    'label' => 'Products Added',
                    'data' => $data['datasets'][0]['data'] ?? [],
                    'borderColor' => Color::Green, 
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => Color::Green,
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 6,
                    'pointHoverRadius' => 8,
                ],
            ],
            'labels' => $data['labels'] ?? [],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                        'font' => [
                            'size' => 12,
                            'weight' => '500',
                        ],
                    ],
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleColor' => '#ffffff',
                    'bodyColor' => '#ffffff',
                    'borderColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderWidth' => 1,
                    'cornerRadius' => 8,
                    'displayColors' => true,
                    'callbacks' => [
                        'label' => "function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' products';
                        }",
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'grid' => [
                        'display' => true,
                        'color' => 'rgba(0, 0, 0, 0.1)',
                        'drawBorder' => false,
                    ],
                    'ticks' => [
                        'color' => 'rgba(0, 0, 0, 0.6)',
                        'font' => [
                            'size' => 12,
                        ],
                        'maxRotation' => 45,
                    ],
                ],
                'y' => [
                    'display' => true,
                    'beginAtZero' => true,
                    'grid' => [
                        'display' => true,
                        'color' => 'rgba(0, 0, 0, 0.1)',
                        'drawBorder' => false,
                    ],
                    'ticks' => [
                        'color' => 'rgba(0, 0, 0, 0.6)',
                        'font' => [
                            'size' => 12,
                        ],
                        'callback' => "function(value) {
                            return value + ' products';
                        }",
                    ],
                ],
            ],
            'elements' => [
                'point' => [
                    'hoverBackgroundColor' => 'rgb(59, 130, 246)',
                    'hoverBorderColor' => '#ffffff',
                    'hoverBorderWidth' => 3,
                ],
                'line' => [
                    'borderJoinStyle' => 'round',
                    'borderCapStyle' => 'round',
                ],
            ],
            'animation' => [
                'duration' => 1000,
                'easing' => 'easeInOutQuart',
            ],
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '5m';
    }

    public function getDescription(): ?string
    {
        return 'Track your product growth momentum with detailed monthly insights';
    }
}