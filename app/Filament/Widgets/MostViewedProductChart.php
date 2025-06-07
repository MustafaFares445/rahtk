<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Services\Filament\ProductStatics;

class MostViewedProductChart extends ChartWidget
{
    protected static ?string $heading = 'Most Viewed Products';

    protected int | string | array $columnSpan = 2;

    protected function getData(): array
    {
        $products = ProductStatics::getMostViewedProducts();

        // Debug: Check if products data exists
        if ($products->isEmpty()) {
            return [
                'labels' => ['No Data Available'],
                'datasets' => [
                    [
                        'label' => 'Views',
                        'data' => [1],
                        'backgroundColor' => '#e5e7eb',
                        'borderColor' => '#d1d5db',
                        'borderWidth' => 1,
                    ]
                ]
            ];
        }

        $labels = $products->pluck('title')->toArray();
        $data = $products->pluck('view')->toArray();

        // Use colors similar to the pie chart
        $colors = [
            '#3b82f6', // Blue
            '#f59e0b', // Orange
            '#ef4444', // Red
            '#06b6d4', // Cyan
            '#6b7280', // Gray
            '#8b5cf6', // Purple
            '#10b981', // Green
            '#f97316', // Orange variant
        ];

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Views',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor' => array_map(fn($color) => $color, array_slice($colors, 0, count($data))),
                    'borderWidth' => 0,
                    'borderRadius' => 4,
                    'borderSkipped' => false,
                ]
            ]
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => false, // Hide legend to match pie chart style
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'display' => true,
                        'color' => '#f3f4f6',
                    ],
                    'ticks' => [
                        'color' => '#6b7280',
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'color' => '#6b7280',
                        'font' => [
                            'size' => 11,
                        ],
                        'maxRotation' => 45,
                        'minRotation' => 0,
                    ],
                ]
            ],
            'elements' => [
                'bar' => [
                    'borderRadius' => 4,
                ]
            ],
        ];
    }
}