<?php

namespace App\Filament\Staff\Resources\DailyEntries\Schemas;

use App\Models\Pump;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Support\RawJs;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class DailyEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Entry details')
                    ->description('Identify the station record before entering today\'s figures.')
                    ->icon('heroicon-o-calendar-days')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('entry_date')->label('Entry date')->required()->default(now()),
                        Hidden::make('staff_id')->default(fn () => auth('staff')->id()),
                        Hidden::make('status')->default('SUBMITTED'),
                    ]),

                Section::make('Pump meter readings')
                    ->description('Enter opening and closing meter readings for every active pump. Litres and totals calculate automatically.')
                    ->icon('heroicon-o-chart-bar-square')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('pumpReadings')
                            ->relationship('pumpReadings')
                            ->label('Active pump readings')
                            ->addActionLabel('Add pump reading')
                            ->default(fn () => Pump::query()->with('product')->where('active', true)->get()->map(fn (Pump $pump): array => [
                                'pump_id' => $pump->id,
                                'base_price_at_entry' => (float) $pump->product->base_price_per_liter,
                                'buying_price_at_entry' => (float) $pump->product->buying_price_per_liter,
                                'unit_selling_price' => (float) $pump->product->base_price_per_liter,
                            ])->all())
                            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => self::prepareReading($data))
                            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array => self::prepareReading($data))
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTotals($get, $set))
                            ->columns(6)
                            ->schema([
                                Select::make('pump_id')
                                    ->label('Pump')
                                    ->options(fn () => Pump::query()->with('product')->where('active', true)->get()->mapWithKeys(fn (Pump $pump) => [$pump->id => $pump->name . ' - ' . $pump->product->type]))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->columnSpan(2)
                                    ->afterStateUpdated(function ($state, Set $set): void {
                                        $basePrice = $state ? (float) (Pump::with('product')->find($state)?->product?->base_price_per_liter ?? 0) : 0;
                                        $buyingPrice = $state ? (float) (Pump::with('product')->find($state)?->product?->buying_price_per_liter ?? 0) : 0;
                                        $set('base_price_at_entry', $basePrice);
                                        $set('buying_price_at_entry', $buyingPrice);
                                        $set('price_variance', false);
                                    }),
                                TextInput::make('opening_reading')->label('Opening')->numeric()->required()->live()->afterStateUpdated(fn (Get $get, Set $set) => self::calculateReading($get, $set))->columnSpan(1),
                                TextInput::make('closing_reading')->label('Closing')->numeric()->required()->live()->afterStateUpdated(fn (Get $get, Set $set) => self::calculateReading($get, $set))->columnSpan(1),
                                TextInput::make('liters_sold')->label('Litres sold')->suffix('L')->numeric()->readOnly()->dehydrated()->default(0)->columnSpan(1),
                                TextInput::make('unit_selling_price')->label('Price / Litre')->prefix('NGN')->numeric()->required()->live()->afterStateUpdated(fn (Get $get, Set $set) => self::calculateReading($get, $set))->columnSpan(1),
                                TextInput::make('total_amount')->label('Total amount')->prefix('NGN')->numeric()->stripCharacters(',')->mask(RawJs::make('$money($input)'))->readOnly()->dehydrated()->default(0)->columnSpan(2),
                                Hidden::make('base_price_at_entry')->default(0),
                                Hidden::make('buying_price_at_entry')->default(0),
                                Hidden::make('price_variance')->default(false),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['pump_id'] ? (Pump::find($state['pump_id'])?->name ?? 'Pump reading') : 'New pump reading'),
                        Section::make('Fuel grand total')
                            ->compact()
                            ->columns(2)
                            ->schema([
                                TextInput::make('fuel_grand_total_liters')->label('Total litres sold')->suffix('L')->numeric()->readOnly()->dehydrated()->default(0)->live(),
                                TextInput::make('fuel_grand_total_amount')->label('Total fuel revenue')->prefix('NGN')->numeric()->stripCharacters(',')->mask(RawJs::make('$money($input)'))->readOnly()->dehydrated()->default(0)->live(),
                            ]),
                    ]),

                Grid::make(2)
                    ->columnSpanFull()
                    ->extraAttributes(['style' => 'gap: 24px;'])
                    ->schema([
                    Section::make('Payment breakdown')
                    ->description('Record how the fuel revenue was received. Physical cash, POS/transfer, and direct bank deposit are tracked separately.')
                    ->icon('heroicon-o-banknotes')
                    ->columns(3)
                    ->schema([
                        TextInput::make('cash_amount')->label('Physical cash collected')->prefix('NGN')->numeric()->default(0)->required()->live(debounce: 500)->helperText('Cash physically received from fuel sales.')->afterStateUpdated(fn (Get $get, Set $set) => self::updatePaymentDifference($get, $set)),
                        TextInput::make('pos_amount')->label('POS / bank transfer')->prefix('NGN')->numeric()->default(0)->required()->live(debounce: 500)->helperText('Electronic payments received at the station.')->afterStateUpdated(fn (Get $get, Set $set) => self::updatePaymentDifference($get, $set)),
                        TextInput::make('bank_deposit_amount')->label('Direct bank deposit')->prefix('NGN')->numeric()->default(0)->required()->live(debounce: 500)->helperText('Payments deposited directly into the bank.')->afterStateUpdated(fn (Get $get, Set $set) => self::updatePaymentDifference($get, $set)),
                        TextInput::make('payment_difference')->label('Reconciliation difference')->prefix('NGN')->readOnly()->dehydrated(false)->default(0)->helperText('For reconciliation visibility only. This does not block submission.')->columnSpanFull(),
                        Placeholder::make('reconciliation_status')
                            ->label('Live status')
                            ->content(fn (Get $get): HtmlString => abs((float) ($get('payment_difference') ?? 0)) < 0.01
                                ? new HtmlString('<span style="display:inline-flex;padding:.35rem .7rem;border-radius:999px;background:#dcfce7;color:#166534;font:700 .78rem Arial,sans-serif">Balanced &#10003;</span>')
                                : new HtmlString('<span style="display:inline-flex;padding:.35rem .7rem;border-radius:999px;background:#fee2e2;color:#991b1b;font:700 .78rem Arial,sans-serif">Unbalanced: Out by NGN ' . number_format(abs((float) ($get('payment_difference') ?? 0)), 2) . '</span>'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Daily expenses')
                    ->description('Record every operating expense incurred during the shift.')
                    ->icon('heroicon-o-receipt-percent')
                    ->schema([
                        Repeater::make('expenses')
                            ->relationship('expenses')
                            ->label('Expense items')
                            ->addActionLabel('Add expense')
                            ->defaultItems(0)
                            ->afterStateUpdated(fn (Get $get, Set $set) => $set('total_expenses', self::sumExpenses($get('expenses'))))
                            ->table([
                                TableColumn::make('Item description')->width('40%'),
                                TableColumn::make('Category')->width('30%'),
                                TableColumn::make('Amount (NGN)')->width('20%'),
                            ])
                            ->schema([
                                TextInput::make('description')->label('Item description')->required(),
                                Select::make('category')->label('Category')->options(['TRANSPORT' => 'Transport', 'MAINTENANCE' => 'Maintenance', 'STAFF' => 'Staff', 'UTILITIES' => 'Utilities', 'OTHER' => 'Other'])->default('OTHER')->required(),
                                TextInput::make('amount')->label('Amount')->prefix('NGN')->numeric()->required()->live()->afterStateUpdated(fn (Get $get, Set $set) => self::updateExpenseTotals($get, $set)),
                            ]),
                        TextInput::make('total_expenses')->label('Expense total')->prefix('NGN')->numeric()->stripCharacters(',')->mask(RawJs::make('$money($input)'))->readOnly()->dehydrated()->default(0),
                    ]),
                    ]),

                Section::make('Net daily reconciliation')
                    ->description('Final figures are calculated from the sales, payments, and expense entries above.')
                    ->icon('heroicon-o-calculator')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        Placeholder::make('net_cash_revenue_display')
                            ->label('Actual net cash revenue')
                            ->content(fn (Get $get): HtmlString => self::moneyCard(self::number($get('fuel_grand_total_amount')) - self::sumExpenses($get('expenses')))),
                        Placeholder::make('gross_profit_display')
                            ->label('Gross profit')
                            ->content(fn (Get $get): HtmlString => self::moneyCard(self::grossProfit($get('pumpReadings')))),
                        Placeholder::make('net_profit_display')
                            ->label('Net profit / loss')
                            ->content(fn (Get $get): HtmlString => self::moneyCard(self::grossProfit($get('pumpReadings')) - self::sumExpenses($get('expenses')), true)),
                        Hidden::make('net_cash_revenue')->default(0),
                        Hidden::make('gross_profit')->default(0),
                        Hidden::make('net_profit')->default(0),
                    ]),
            ]);
    }

    private static function calculateReading(Get $get, Set $set): void
    {
        $opening = self::number($get('opening_reading'));
        $closing = self::number($get('closing_reading'));
        $price = self::number($get('unit_selling_price'));
        $basePrice = self::number($get('base_price_at_entry'));
        $liters = max(0, $closing - $opening);

        $set('liters_sold', round($liters, 2));
        $set('total_amount', round($liters * $price, 2));
        $set('price_variance', $price > $basePrice && $basePrice > 0);

        $readings = $get('../../pumpReadings') ?? [];
        $fuelAmount = self::sumReadings($readings, 'total_amount');
        $fuelLiters = self::sumReadings($readings, 'liters_sold');
        $grossProfit = self::grossProfit($readings);
        $expenses = self::sumExpenses($get('../../expenses'));

        $set('../../fuel_grand_total_liters', $fuelLiters);
        $set('../../fuel_grand_total_amount', $fuelAmount);
        $set('../../total_expenses', $expenses);
        $set('../../net_cash_revenue', round($fuelAmount - $expenses, 2));
        $set('../../gross_profit', $grossProfit);
        $set('../../net_profit', round($grossProfit - $expenses, 2));
        self::updatePaymentDifferenceAtRoot($get, $set, $fuelAmount);
    }

    private static function prepareReading(array $data): array
    {
        $opening = self::number($data['opening_reading'] ?? 0);
        $closing = self::number($data['closing_reading'] ?? 0);
        $price = self::number($data['unit_selling_price'] ?? 0);
        $basePrice = self::number($data['base_price_at_entry'] ?? 0);
        $liters = max(0, $closing - $opening);

        $data['liters_sold'] = round($liters, 2);
        $data['total_amount'] = round($liters * $price, 2);
        $data['price_variance'] = $basePrice > 0 && $price > $basePrice;

        return $data;
    }

    private static function updateTotals(Get $get, Set $set): void
    {
        $readings = $get('pumpReadings') ?? [];
        $set('fuel_grand_total_liters', self::sumReadings($readings, 'liters_sold'));
        $set('fuel_grand_total_amount', self::sumReadings($readings, 'total_amount'));
    }

    private static function updateExpenseTotals(Get $get, Set $set): void
    {
        $expenses = self::sumExpenses($get('../../expenses'));
        $fuelAmount = self::number($get('../../fuel_grand_total_amount'));
        $grossProfit = self::number($get('../../gross_profit'));

        $set('../../total_expenses', $expenses);
        $set('../../net_cash_revenue', round($fuelAmount - $expenses, 2));
        $set('../../net_profit', round($grossProfit - $expenses, 2));
    }

    private static function sumExpenses(?array $expenses): float
    {
        $total = 0;
        foreach ($expenses ?? [] as $expense) {
            if (is_array($expense)) {
                $total += self::number($expense['amount'] ?? 0);
            }
        }

        return round($total, 2);
    }

    private static function grossProfit(mixed $readings): float
    {
        $total = 0;
        foreach (is_array($readings) ? $readings : [] as $reading) {
            if (is_array($reading)) {
                $total += (self::number($reading['unit_selling_price'] ?? 0) - self::number($reading['buying_price_at_entry'] ?? 0)) * self::number($reading['liters_sold'] ?? 0);
            }
        }

        return round($total, 2);
    }

    private static function moneyCard(float $amount, bool $lossAware = false): HtmlString
    {
        $class = $lossAware && $amount < 0 ? 'color:#b91c1c;background:#fef2f2;' : 'color:#0f172a;background:#f8fafc;';

        return new HtmlString('<span style="display:block;padding:.75rem 1rem;border:1px solid #dbe3ee;border-radius:.5rem;' . $class . 'font:700 1.05rem Arial,sans-serif">NGN ' . number_format($amount, 2) . '</span>');
    }

    private static function updatePaymentDifference(Get $get, Set $set): void
    {
        $payments = self::number($get('cash_amount')) + self::number($get('pos_amount')) + self::number($get('bank_deposit_amount'));
        $fuelTotal = self::number($get('fuel_grand_total_amount'));
        $set('payment_difference', round($payments - $fuelTotal, 2));
    }

    private static function updatePaymentDifferenceAtRoot(Get $get, Set $set, float $fuelTotal): void
    {
        $payments = self::number($get('../../cash_amount')) + self::number($get('../../pos_amount')) + self::number($get('../../bank_deposit_amount'));
        $set('../../payment_difference', round($payments - $fuelTotal, 2));
    }

    private static function sumReadings(mixed $readings, string $key): float
    {
        $total = 0;
        foreach (is_array($readings) ? $readings : [] as $reading) {
            if (is_array($reading)) {
                $total += self::number($reading[$key] ?? 0);
            }
        }

        return round($total, 2);
    }

    private static function number(mixed $value): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            return (float) str_replace([',', 'NGN', ' '], '', $value);
        }

        return 0;
    }
}
