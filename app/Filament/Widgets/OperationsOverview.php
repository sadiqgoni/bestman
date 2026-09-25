<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OperationsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Team members', User::query()->count())
                ->description('Active Bestman accounts')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Administrators', User::query()->where('role', 'ADMIN')->count())
                ->description('Operations access')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('success'),
            Stat::make('Staff accounts', User::query()->where('role', 'STAFF')->count())
                ->description('Staff portal access')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
            Stat::make('System status', 'Online')
                ->description('Laravel Filament is ready')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
