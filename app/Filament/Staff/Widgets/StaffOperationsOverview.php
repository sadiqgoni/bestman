<?php

namespace App\Filament\Staff\Widgets;

use App\Models\DailyEntry;
use App\Models\Pump;
use App\Models\Tank;
use App\Models\TankerDelivery;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StaffOperationsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $stockLiters = (float) Tank::query()->where('active', true)->sum('current_stock_liters');
        $todayEntries = DailyEntry::query()->whereDate('entry_date', today())->count();
        $pendingDeliveries = TankerDelivery::query()->where('status', 'PENDING')->count();
        $activePumps = Pump::query()->where('active', true)->count();

        return [
            Stat::make('Current stock', number_format($stockLiters, 0) . ' L')
                ->description('Across active tanks')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('primary'),
            Stat::make('Today\'s entries', $todayEntries)
                ->description('Daily records submitted')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('success'),
            Stat::make('Pending deliveries', $pendingDeliveries)
                ->description('Awaiting admin confirmation')
                ->descriptionIcon('heroicon-m-truck')
                ->color($pendingDeliveries > 0 ? 'warning' : 'success'),
            Stat::make('Active pumps', $activePumps)
                ->description('Configured for operations')
                ->descriptionIcon('heroicon-m-adjustments-horizontal')
                ->color('info'),
        ];
    }
}
