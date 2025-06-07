<?php

namespace App\Services\Filament;

use App\Enums\ProductTypes;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ProductStatics
{
    /**
     * Cache duration in minutes
     */
    private const CACHE_DURATION = 60;

    /**
     * Type badge colors mapping
     */
    private static array $typeBadgeColors = [
        'estate' => 'primary',
        'car' => 'warning',
        'electronic' => 'danger',
        'farm' => 'info',
        'building' => 'gray',
    ];

    /* ==================== BASIC STATISTICS ==================== */

    /**
     * Get total number of products
     */
    public static function getTotalProducts(): int
    {
        return Cache::remember('products.total', self::CACHE_DURATION, function () {
            return Product::count();
        });
    }

    /**
     * Get products count by type
     */
    public static function getProductsByType(): array
    {
        return Cache::remember('products.by_type', self::CACHE_DURATION, function () {
            return Product::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray();
        });
    }

    /**
     * Get urgent products count
     */
    public static function getUrgentProductsCount(): int
    {
        return Cache::remember('products.urgent', self::CACHE_DURATION, function () {
            return Product::where('is_urgent', true)->count();
        });
    }

    /**
     * Get products created today
     */
    public static function getTodayProductsCount(): int
    {
        return Cache::remember('products.today', 30, function () { // Shorter cache for daily data
            return Product::whereDate('created_at', today())->count();
        });
    }

    /**
     * Get products created this week
     */
    public static function getWeeklyProductsCount(): int
    {
        return Cache::remember('products.weekly', self::CACHE_DURATION, function () {
            return Product::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count();
        });
    }

    /**
     * Get products created this month
     */
    public static function getMonthlyProductsCount(): int
    {
        return Cache::remember('products.monthly', self::CACHE_DURATION, function () {
            return Product::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
        });
    }

    /* ==================== PRICE ANALYTICS ==================== */

    /**
     * Get average product price
     */
    public static function getAveragePrice(): float
    {
        return Cache::remember('products.avg_price', self::CACHE_DURATION, function () {
            return round(Product::avg('price') ?? 0, 2);
        });
    }

    /**
     * Get average price by type
     */
    public static function getAveragePriceByType(): array
    {
        return Cache::remember('products.avg_price_by_type', self::CACHE_DURATION, function () {
            return Product::select('type', DB::raw('AVG(price) as avg_price'))
                ->groupBy('type')
                ->get()
                ->mapWithKeys(fn($item) => [$item->type => round($item->avg_price, 2)])
                ->toArray();
        });
    }

    /**
     * Get highest priced product
     */
    public static function getHighestPricedProduct(): ?Product
    {
        return Cache::remember('products.highest_price', self::CACHE_DURATION, function () {
            return Product::orderBy('price', 'desc')->first();
        });
    }

    /**
     * Get lowest priced product
     */
    public static function getLowestPricedProduct(): ?Product
    {
        return Cache::remember('products.lowest_price', self::CACHE_DURATION, function () {
            return Product::orderBy('price', 'asc')->first();
        });
    }

    /**
     * Get price range distribution
     */
    public static function getPriceRangeDistribution(): array
    {
        return Cache::remember('products.price_ranges', self::CACHE_DURATION, function () {
            $ranges = [
                '0-100' => [0, 100],
                '101-500' => [101, 500],
                '501-1000' => [501, 1000],
                '1001-5000' => [1001, 5000],
                '5001-10000' => [5001, 10000],
                '10000+' => [10001, PHP_INT_MAX],
            ];

            $distribution = [];
            foreach ($ranges as $label => $range) {
                $count = Product::whereBetween('price', $range)->count();
                $distribution[$label] = $count;
            }

            return $distribution;
        });
    }

    /* ==================== ENGAGEMENT & PERFORMANCE ==================== */

    /**
     * Get most viewed products
     */
    public static function getMostViewedProducts(int $limit = 5): Collection
    {
        return Product::orderBy('view', 'desc')
                ->limit($limit)
                ->get();
    }

    /**
     * Get products with discounts
     */
    public static function getDiscountedProductsCount(): int
    {
        return Cache::remember('products.discounted', self::CACHE_DURATION, function () {
            return Product::whereNotNull('discount')
                ->where('discount', '>', 0)
                ->count();
        });
    }

    /**
     * Get total discount amount
     */
    public static function getTotalDiscountAmount(): float
    {
        return Cache::remember('products.total_discount', self::CACHE_DURATION, function () {
            return round(Product::whereNotNull('discount')->sum('discount') ?? 0, 2);
        });
    }

    /**
     * Get total views across all products
     */
    public static function getTotalViews(): int
    {
        return Cache::remember('products.total_views', self::CACHE_DURATION, function () {
            return Product::sum('view') ?? 0;
        });
    }

    /* ==================== GEOGRAPHIC & LOCATION ==================== */

    /**
     * Get products distribution by location (top cities)
     */
    public static function getTopLocationsByProductCount(int $limit = 10): array
    {
        return Cache::remember("products.top_locations_{$limit}", self::CACHE_DURATION, function () use ($limit) {
            return Product::select(DB::raw('SUBSTRING_INDEX(address, ",", 1) as city'), DB::raw('count(*) as count'))
                ->groupBy('city')
                ->orderBy('count', 'desc')
                ->limit($limit)
                ->pluck('count', 'city')
                ->toArray();
        });
    }

    /* ==================== RECENT & TRENDING ==================== */

    /**
     * Get recent products
     */
    public static function getRecentProducts(int $limit = 5): Collection
    {
        return Cache::remember("products.recent_{$limit}", 30, function () use ($limit) { // Shorter cache for recent data
            return Product::with(['media' => fn($query) => $query->where('collection_name', 'images')->limit(1)])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get products created per day for the last 30 days
     */
    public static function getProductsPerDayLast30Days(): array
    {
        return Cache::remember('products.daily_last_30', self::CACHE_DURATION, function () {
            $results = Product::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('count(*) as count')
                )
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return $results->mapWithKeys(fn($item) => [$item->date => $item->count])->toArray();
        });
    }

    /**
     * Get products growth trend (monthly for last 12 months)
     */
    public static function getProductsGrowthTrend(): array
    {
        return Cache::remember('products.growth_trend', self::CACHE_DURATION * 2, function () {
            $trend = [];

            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $count = Product::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $trend[$date->format('M Y')] = $count;
            }

            return $trend;
        });
    }

    /* ==================== COMPREHENSIVE ANALYTICS ==================== */

    /**
     * Get comprehensive dashboard statistics
     */
    public static function getDashboardStats(): array
    {
        return Cache::remember('products.dashboard_stats', 30, function () { // Shorter cache for dashboard
            return [
                'overview' => [
                    'total_products' => self::getTotalProducts(),
                    'urgent_products' => self::getUrgentProductsCount(),
                    'today_products' => self::getTodayProductsCount(),
                    'weekly_products' => self::getWeeklyProductsCount(),
                    'monthly_products' => self::getMonthlyProductsCount(),
                    'total_views' => self::getTotalViews(),
                ],
                'pricing' => [
                    'average_price' => self::getAveragePrice(),
                    'discounted_products' => self::getDiscountedProductsCount(),
                    'total_discount_amount' => self::getTotalDiscountAmount(),
                    'price_distribution' => self::getPriceRangeDistribution(),
                ],
                'distribution' => [
                    'products_by_type' => self::getProductsByType(),
                    'average_price_by_type' => self::getAveragePriceByType(),
                    'top_locations' => self::getTopLocationsByProductCount(),
                ],
                'trending' => [
                    'recent_products' => self::getRecentProducts(),
                    'most_viewed_products' => self::getMostViewedProducts(),
                    'growth_trend' => self::getProductsGrowthTrend(),
                ],
            ];
        });
    }

    /**
     * Get product type statistics with additional metrics
     */
    public static function getTypeStatistics(): array
    {
        return Cache::remember('products.type_statistics', self::CACHE_DURATION, function () {
            $stats = [];

            foreach (ProductTypes::cases() as $type) {
                if ($type->value === 'school') continue;

                $typeProducts = Product::where('type', $type->value);

                $stats[$type->value] = [
                    'total_count' => $typeProducts->count(),
                    'urgent_count' => $typeProducts->where('is_urgent', true)->count(),
                    'average_price' => round($typeProducts->avg('price') ?? 0, 2),
                    'highest_price' => round($typeProducts->max('price') ?? 0, 2),
                    'lowest_price' => round($typeProducts->min('price') ?? 0, 2),
                    'total_views' => $typeProducts->sum('view') ?? 0,
                    'discounted_count' => $typeProducts->whereNotNull('discount')->where('discount', '>', 0)->count(),
                    'badge_color' => self::$typeBadgeColors[$type->value] ?? 'secondary',
                    'percentage' => 0, // Will be calculated separately
                ];
            }

            // Calculate percentages
            $totalProducts = array_sum(array_column($stats, 'total_count'));
            if ($totalProducts > 0) {
                foreach ($stats as $type => $data) {
                    $stats[$type]['percentage'] = round(($data['total_count'] / $totalProducts) * 100, 1);
                }
            }

            return $stats;
        });
    }

    /* ==================== WIDGET SPECIFIC METHODS ==================== */

    /**
     * Get stats for overview widget
     */
    public static function getOverviewWidgetStats(): array
    {
        return [
            [
                'label' => 'Total Products',
                'value' => number_format(self::getTotalProducts()),
                'icon' => 'heroicon-o-shopping-bag',
                'color' => 'primary',
            ],
            [
                'label' => 'Urgent Products',
                'value' => number_format(self::getUrgentProductsCount()),
                'icon' => 'heroicon-o-bolt',
                'color' => 'danger',
            ],
            [
                'label' => 'Average Price',
                'value' => '$' . number_format(self::getAveragePrice(), 2),
                'icon' => 'heroicon-o-currency-dollar',
                'color' => 'success',
            ],
            [
                'label' => 'Total Views',
                'value' => number_format(self::getTotalViews()),
                'icon' => 'heroicon-o-eye',
                'color' => 'info',
            ],
        ];
    }

    /**
     * Get chart data for product growth
     */
    public static function getGrowthChartData(): array
    {
        $trend = self::getProductsGrowthTrend();

        return [
            'labels' => array_keys($trend),
            'datasets' => [
                [
                    'label' => 'Products Created',
                    'data' => array_values($trend),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ]
            ]
        ];
    }

    /**
     * Get chart data for type distribution
     */
    public static function getTypeDistributionChartData(): array
    {
        $types = self::getProductsByType();
        $colors = [
            '#3B82F6', // Blue
            '#F59E0B', // Amber
            '#EF4444', // Red
            '#06B6D4', // Cyan
            '#6B7280', // Gray
        ];

        return [
            'labels' => array_map('ucfirst', array_keys($types)),
            'datasets' => [
                [
                    'data' => array_values($types),
                    'backgroundColor' => array_slice($colors, 0, count($types)),
                ]
            ]
        ];
    }


    /* ==================== CACHE MANAGEMENT ==================== */

    /**
     * Clear all dashboard cache
     */
    public static function clearCache(): void
    {
        $cacheKeys = [
            'products.total',
            'products.by_type',
            'products.urgent',
            'products.today',
            'products.weekly',
            'products.monthly',
            'products.avg_price',
            'products.avg_price_by_type',
            'products.highest_price',
            'products.lowest_price',
            'products.price_ranges',
            'products.discounted',
            'products.total_discount',
            'products.total_views',
            'products.dashboard_stats',
            'products.type_statistics',
            'products.daily_last_30',
            'products.growth_trend',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear pattern-based cache keys
        Cache::flush(); // Use this cautiously in production
    }

    /**
     * Refresh specific stat cache
     */
    public static function refreshStat(string $stat): void
    {
        Cache::forget("products.{$stat}");
    }
}