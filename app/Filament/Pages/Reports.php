<?php

namespace App\Filament\Pages;

use App\Models\DailyEntry;
use App\Models\TankerDelivery;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Reports extends Page
{
    protected string $view = 'filament.pages.reports';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Reports & Alerts';

    protected static ?int $navigationSort = 30;

    protected function getViewData(): array
    {
        return [
            'entries' => DailyEntry::query()->with(['staff', 'pumpReadings.pump', 'expenses'])->latest('entry_date')->latest()->limit(20)->get(),
            'alerts' => [
                'priceVariances' => \App\Models\PumpReading::query()->with(['dailyEntry.staff', 'pump'])->where('price_variance', true)->latest()->limit(10)->get(),
                'deliveryShortages' => TankerDelivery::query()->with(['product', 'tank'])->where('variance_liters', '>', 0)->latest()->limit(10)->get(),
                'pendingDeliveries' => TankerDelivery::query()->with(['product', 'tank'])->where('status', 'PENDING')->latest()->limit(10)->get(),
            ],
        ];
    }
}
