<?php

namespace App\Filament\Staff\Resources\DailyEntries\Pages;

use App\Filament\Concerns\RedirectsToIndex;
use App\Filament\Staff\Resources\DailyEntries\DailyEntryResource;
use App\Models\DailyEntry;
use Illuminate\Validation\ValidationException;
use Filament\Resources\Pages\CreateRecord;

class CreateDailyEntry extends CreateRecord
{
    use RedirectsToIndex;

    protected static string $resource = DailyEntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $entryDate = $data['entry_date'] ?? null;
        $staffId = auth('staff')->id();

        if ($entryDate && $staffId && DailyEntry::query()->where('staff_id', $staffId)->whereDate('entry_date', $entryDate)->exists()) {
            throw ValidationException::withMessages([
                'data.entry_date' => 'A daily entry already exists for this staff member on this date. Open the existing entry from Daily Entries to edit it.',
            ]);
        }

        $readings = collect($data['pumpReadings'] ?? [])->map(function (array $reading): array {
            $opening = self::number($reading['opening_reading'] ?? 0);
            $closing = self::number($reading['closing_reading'] ?? 0);
            $liters = max(0, $closing - $opening);
            $price = self::number($reading['unit_selling_price'] ?? 0);
            $basePrice = self::number($reading['base_price_at_entry'] ?? 0);
            $buyingPrice = self::number($reading['buying_price_at_entry'] ?? 0);

            $reading['liters_sold'] = round($liters, 2);
            $reading['total_amount'] = round($liters * $price, 2);
            $reading['price_variance'] = $basePrice > 0 && $price > $basePrice;

            return $reading;
        });

        $fuelLiters = 0;
        $fuelAmount = 0;
        foreach ($readings as $reading) {
            if (is_array($reading)) {
                $fuelLiters += self::number($reading['liters_sold'] ?? 0);
                $fuelAmount += self::number($reading['total_amount'] ?? 0);
            }
        }
        $fuelLiters = round($fuelLiters, 2);
        $fuelAmount = round($fuelAmount, 2);
        $expenses = collect($data['expenses'] ?? []);
        $totalExpenses = 0;
        foreach ($expenses as $expense) {
            if (is_array($expense)) {
                $totalExpenses += self::number($expense['amount'] ?? 0);
            }
        }
        $totalExpenses = round($totalExpenses, 2);
            $grossProfit = 0;
            foreach ($readings as $reading) {
                if (is_array($reading)) {
                    $grossProfit += (self::number($reading['unit_selling_price'] ?? 0) - self::number($reading['buying_price_at_entry'] ?? 0)) * self::number($reading['liters_sold'] ?? 0);
                }
            }
            $grossProfit = round($grossProfit, 2);

        $data['staff_id'] = auth('staff')->id();
        $data['status'] = 'SUBMITTED';
        $data['fuel_grand_total_liters'] = $fuelLiters;
        $data['fuel_grand_total_amount'] = $fuelAmount;
        $data['total_expenses'] = $totalExpenses;
        $data['net_cash_revenue'] = round($fuelAmount - $totalExpenses, 2);
        $data['gross_profit'] = $grossProfit;
        $data['net_profit'] = round($grossProfit - $totalExpenses, 2);

        return $data;
    }

    private static function number(mixed $value): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        return is_string($value) ? (float) str_replace([',', 'NGN', ' '], '', $value) : 0;
    }
}
