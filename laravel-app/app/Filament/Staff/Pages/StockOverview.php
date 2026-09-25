<?php

namespace App\Filament\Staff\Pages;

use App\Models\Tank;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class StockOverview extends Page
{
    protected string $view = 'filament.staff.pages.stock-overview';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Stock Overview';

    protected static ?int $navigationSort = 5;

    protected function getViewData(): array
    {
        return [
            'tanks' => Tank::query()
                ->with('product')
                ->where('active', true)
                ->orderBy('name')
                ->get(),
        ];
    }
}
