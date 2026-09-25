<?php

namespace App\Filament\Pages;

use App\Models\DailyEntry;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ProfitLoss extends Page
{
    protected string $view = 'filament.pages.profit-loss';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Profit & Loss';

    protected static ?int $navigationSort = 40;

    protected function getViewData(): array
    {
        $entries = DailyEntry::query()->with(['staff', 'pumpReadings.pump.product', 'expenses'])->latest('entry_date')->latest()->limit(50)->get();
        $totals = ['revenue' => 0, 'expenses' => 0, 'grossProfit' => 0, 'netProfit' => 0];

        foreach ($entries as $entry) {
            $revenue = 0;
            $expenses = 0;
            $grossProfit = 0;

            foreach ($entry->pumpReadings as $reading) {
                $liters = (float) $reading->liters_sold;
                $revenue += (float) $reading->total_amount;
                $grossProfit += ((float) $reading->unit_selling_price - (float) ($reading->pump->product->buying_price_per_liter ?? 0)) * $liters;
            }

            foreach ($entry->expenses as $expense) {
                $expenses += (float) $expense->amount;
            }

            $entry->report_revenue = round($revenue, 2);
            $entry->report_expenses = round($expenses, 2);
            $entry->report_gross_profit = round($grossProfit, 2);
            $entry->report_net_profit = round($grossProfit - $expenses, 2);
            $totals['revenue'] += $revenue;
            $totals['expenses'] += $expenses;
            $totals['grossProfit'] += $grossProfit;
            $totals['netProfit'] += $grossProfit - $expenses;
        }

        return [
            'entries' => $entries,
            'totals' => array_map(fn (float $value): float => round($value, 2), $totals),
        ];
    }
}
