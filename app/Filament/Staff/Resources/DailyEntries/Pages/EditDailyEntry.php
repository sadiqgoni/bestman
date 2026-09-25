<?php

namespace App\Filament\Staff\Resources\DailyEntries\Pages;

use App\Filament\Concerns\RedirectsToIndex;
use App\Filament\Staff\Resources\DailyEntries\DailyEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDailyEntry extends EditRecord
{
    use RedirectsToIndex;

    protected static string $resource = DailyEntryResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $fuelLiters = 0;
        $fuelAmount = 0;
        $grossProfit = 0;

        foreach ($data['pumpReadings'] ?? [] as $reading) {
            if (! is_array($reading)) {
                continue;
            }

            $opening = self::number($reading['opening_reading'] ?? 0);
            $closing = self::number($reading['closing_reading'] ?? 0);
            $liters = max(0, $closing - $opening);
            $price = self::number($reading['unit_selling_price'] ?? 0);
            $buyingPrice = self::number($reading['buying_price_at_entry'] ?? 0);
            $fuelLiters += $liters;
            $fuelAmount += $liters * $price;
            $grossProfit += ($price - $buyingPrice) * $liters;
        }

        $expenses = 0;
        foreach ($data['expenses'] ?? [] as $expense) {
            if (is_array($expense)) {
                $expenses += self::number($expense['amount'] ?? 0);
            }
        }

        $data['fuel_grand_total_liters'] = round($fuelLiters, 2);
        $data['fuel_grand_total_amount'] = round($fuelAmount, 2);
        $data['total_expenses'] = round($expenses, 2);
        $data['net_cash_revenue'] = round($fuelAmount - $expenses, 2);
        $data['gross_profit'] = round($grossProfit, 2);
        $data['net_profit'] = round($grossProfit - $expenses, 2);

        return $data;
    }

    private static function number(mixed $value): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        return is_string($value) ? (float) str_replace([',', 'NGN', ' '], '', $value) : 0;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
