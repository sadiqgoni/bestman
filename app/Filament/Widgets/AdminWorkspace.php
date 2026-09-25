<?php

namespace App\Filament\Widgets;

use App\Models\PumpReading;
use App\Models\TankerDelivery;
use Filament\Widgets\Widget;

class AdminWorkspace extends Widget
{
    protected string $view = 'filament.widgets.admin-workspace';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function getViewData(): array
    {
        return [
            'priceAlerts' => PumpReading::query()->where('price_variance', true)->count(),
            'pendingDeliveries' => TankerDelivery::query()->where('status', 'PENDING')->count(),
        ];
    }
}
