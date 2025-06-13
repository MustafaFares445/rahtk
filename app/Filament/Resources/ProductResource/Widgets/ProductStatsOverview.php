<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Enums\ProductTypes;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ProductStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $totalProducts = Product::where('type', '!=', ProductTypes::SCHOOL->value)->count();
        $urgentProducts = Product::where('is_urgent', true)
            ->where('type', '!=', ProductTypes::SCHOOL->value)
            ->count();

        $averagePrice = Product::where('type', '!=', ProductTypes::SCHOOL->value)
            ->avg('price');

        $typeCounts = Product::select('type', DB::raw('count(*) as total'))
            ->where('type', '!=', ProductTypes::SCHOOL->value)
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        return [
            Stat::make('Total Products', $totalProducts)
                ->label('عدد المنتجات')
                ->description('جميع أنواع المنتجات')
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->chart($this->getChartData($typeCounts))
                ->color('primary'),

            Stat::make('Urgent Products', $urgentProducts)
                ->label('المنتحات العاجلة')
                ->description('عدد المنتجات العاجلة')
                ->descriptionIcon('heroicon-o-bolt')
                ->color('warning'),

            Stat::make('Avg. Price', number_format($averagePrice, 2) . ' $')
                ->label('السعر المتوسط')
                ->description('المتوسط عبر جميع المنتجات')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success'),
        ];
    }

    private function getChartData(array $typeCounts): array
    {
        $data = [];
        foreach (ProductTypes::cases() as $type) {
            if ($type === ProductTypes::SCHOOL) continue;
            $data[] = $typeCounts[$type->value] ?? 0;
        }
        return $data;
    }
}