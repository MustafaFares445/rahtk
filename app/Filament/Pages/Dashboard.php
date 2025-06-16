<?php

namespace App\Filament\Pages;

use Filament\Forms;
use App\Models\Contact;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Filament\Widgets\ContactInfoWidget;
use App\Filament\Widgets\ProductGrowthChart;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\MostViewedProductChart;
use App\Filament\Widgets\ProductTypeDistributionChart;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public function getWidgets(): array
    {
        return [
            ContactInfoWidget::class,
            ProductTypeDistributionChart::class,
            MostViewedProductChart::class,
            ProductGrowthChart::class,
        ];
    }
}