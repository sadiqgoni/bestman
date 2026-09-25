<x-filament-panels::page>
    <style>
        .bm-report-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .bm-report-shell { padding: 1.25rem; border-radius: 1rem; background: linear-gradient(145deg, #f4f8fc 0%, #eef4fa 100%); }
        .bm-report-card { padding: 1.1rem 1.25rem; border: 1px solid #d6e2ef; border-radius: .75rem; background: #fff; box-shadow: 0 8px 18px rgba(15,23,42,.05); }
        .bm-report-card h3 { margin: 0; color: #0f172a; font: 700 1.15rem Georgia, serif; }
        .bm-report-card p { margin: .5rem 0 0; color: #64748b; font: .95rem/1.55 Arial, sans-serif; }
        .bm-alert { margin-top: 1rem; padding: 1.1rem 1.25rem; border-left: 4px solid #d5222a; border-radius: .5rem; background: #fff1f2; color: #881337; font: 1rem/1.5 Arial, sans-serif; }
        .bm-alert--warning { border-color: #d97706; background: #fffbeb; color: #92400e; }
        .bm-report-table { width: 100%; border-collapse: collapse; font: .98rem Arial, sans-serif; }
        .bm-report-table th, .bm-report-table td { padding: .95rem .7rem; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .bm-report-table th { color: #334155; background: #f1f5f9; font-size: .78rem; text-transform: uppercase; letter-spacing: .05em; }
        .bm-report-table td:nth-child(n+3), .bm-report-table th:nth-child(n+3) { text-align: right; }
        .bm-report-table td:nth-child(n+3) { font-weight: 700; color: #0f172a; }
        .bm-loss { color: #d5222a; font-weight: 700; }
        .bm-detail-list { display: grid; gap: 1rem; margin-top: 1.5rem; }
        .bm-detail-card { padding: 1.35rem; border: 1px solid #d6e2ef; border-radius: .85rem; background: #fff; box-shadow: 0 10px 24px rgba(15,23,42,.06); }
        .bm-detail-header { display: flex; justify-content: space-between; gap: 1rem; align-items: flex-start; padding-bottom: .9rem; border-bottom: 1px solid #e5e7eb; }
        .bm-detail-header h3 { margin: 0; color: #0f172a; font: 700 1.25rem Georgia, serif; }
        .bm-detail-header p { margin: .35rem 0 0; color: #64748b; font: .95rem Arial, sans-serif; }
        .bm-detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem 2.25rem; margin-top: 1.25rem; }
        .bm-detail-title { margin: 0 0 .7rem; color: #0f766e; font: 700 .88rem Arial, sans-serif; text-transform: uppercase; letter-spacing: .06em; }
        .bm-mini-table { width: 100%; border-collapse: collapse; font: .92rem Arial, sans-serif; }
        .bm-mini-table th, .bm-mini-table td { padding: .62rem .45rem; border-bottom: 1px solid #eef2f7; text-align: left; }
        .bm-mini-table th { color: #475569; background: #f8fafc; font-size: .74rem; text-transform: uppercase; }
        .bm-mini-table td:last-child, .bm-mini-table th:last-child { text-align: right; font-weight: 700; }
        .bm-empty { color: #94a3b8; font: .92rem Arial, sans-serif; }
        @media (max-width: 800px) { .bm-detail-grid { grid-template-columns: 1fr; } }
        @media (max-width: 800px) { .bm-report-grid { grid-template-columns: 1fr; } .bm-report-table { min-width: 650px; } }
    </style>
    <div class="bm-report-shell">
    <div class="bm-report-grid">
        <div class="bm-report-card"><h3>{{ $entries->count() }} daily records</h3><p>Latest submitted station activity.</p></div>
        <div class="bm-report-card"><h3>{{ $alerts['priceVariances']->count() }} price alerts</h3><p>Actual prices above the configured base price.</p></div>
        <div class="bm-report-card"><h3>{{ $alerts['pendingDeliveries']->count() }} pending deliveries</h3><p>Deliveries waiting for confirmation.</p></div>
    </div>
    @if ($alerts['priceVariances']->isNotEmpty())<div class="bm-alert"><strong>Price variance alert:</strong> {{ $alerts['priceVariances']->count() }} pump reading(s) were entered above the Admin base price.</div>@endif
    @if ($alerts['deliveryShortages']->isNotEmpty())<div class="bm-alert bm-alert--warning"><strong>Delivery shortage alert:</strong> {{ $alerts['deliveryShortages']->count() }} delivery record(s) show a positive waybill-to-received variance.</div>@endif
    <x-filament::section heading="Daily report" description="Fuel sales, reconciliation, expenses, and profit for submitted entries.">
        <div style="overflow-x:auto"><table class="bm-report-table"><thead><tr><th>Date</th><th>Staff</th><th>Fuel revenue</th><th>Expenses</th><th>Net revenue</th><th>Net profit / loss</th></tr></thead><tbody>
            @forelse ($entries as $entry)
                @php
                    $reportFuel = (float) $entry->pumpReadings->sum('total_amount');
                    $reportExpenses = (float) $entry->expenses->sum('amount');
                    $reportGross = (float) $entry->pumpReadings->sum(fn ($reading) => ((float) $reading->unit_selling_price - (float) ($reading->pump->product->buying_price_per_liter ?? 0)) * (float) $reading->liters_sold);
                    $reportNetRevenue = $reportFuel - $reportExpenses;
                    $reportNetProfit = $reportGross - $reportExpenses;
                @endphp
                <tr><td>{{ $entry->entry_date->format('d M Y') }}</td><td>{{ $entry->staff->name ?? 'Unknown' }}</td><td>NGN {{ number_format($reportFuel, 2) }}</td><td>NGN {{ number_format($reportExpenses, 2) }}</td><td>NGN {{ number_format($reportNetRevenue, 2) }}</td><td class="{{ $reportNetProfit < 0 ? 'bm-loss' : '' }}">NGN {{ number_format($reportNetProfit, 2) }}</td></tr>
            @empty<tr><td colspan="6">No daily entries have been submitted yet.</td></tr>@endforelse
    </tbody></table></div>
    </x-filament::section>

    <div class="bm-detail-list">
        @foreach ($entries as $entry)
            <article class="bm-detail-card">
                <div class="bm-detail-header">
                    <div><h3>{{ $entry->entry_date->format('d M Y') }} · {{ $entry->staff->name ?? 'Unknown staff' }}</h3><p>Complete station activity, payments, pump readings, and expenses.</p></div>
                    @php
                        $detailFuel = (float) $entry->pumpReadings->sum('total_amount');
                        $detailExpenses = (float) $entry->expenses->sum('amount');
                        $detailGross = (float) $entry->pumpReadings->sum(fn ($reading) => ((float) $reading->unit_selling_price - (float) ($reading->pump->product->buying_price_per_liter ?? 0)) * (float) $reading->liters_sold);
                        $detailNetRevenue = $detailFuel - $detailExpenses;
                        $detailNetProfit = $detailGross - $detailExpenses;
                    @endphp
                    <strong class="{{ $detailNetProfit < 0 ? 'bm-loss' : '' }}">Net: NGN {{ number_format($detailNetProfit, 2) }}</strong>
                </div>
                <div class="bm-detail-grid">
                    <div>
                        <h4 class="bm-detail-title">Pump meter readings</h4>
                        @if ($entry->pumpReadings->isEmpty())
                            <div class="bm-empty">No pump readings recorded.</div>
                        @else
                            <table class="bm-mini-table"><thead><tr><th>Pump</th><th>Opening</th><th>Closing</th><th>Litres</th><th>Price</th><th>Total</th></tr></thead><tbody>
                                @foreach ($entry->pumpReadings as $reading)
                                    <tr><td>{{ $reading->pump->name ?? 'Pump' }}</td><td>{{ number_format((float) $reading->opening_reading, 2) }}</td><td>{{ number_format((float) $reading->closing_reading, 2) }}</td><td>{{ number_format((float) $reading->liters_sold, 2) }} L</td><td>NGN {{ number_format((float) $reading->unit_selling_price, 2) }}</td><td>NGN {{ number_format((float) $reading->total_amount, 2) }}</td></tr>
                                @endforeach
                            </tbody></table>
                        @endif
                    </div>
                    <div>
                        <h4 class="bm-detail-title">Payment breakdown</h4>
                        <table class="bm-mini-table"><tbody><tr><td>Physical cash</td><td>NGN {{ number_format((float) $entry->cash_amount, 2) }}</td></tr><tr><td>POS / bank transfer</td><td>NGN {{ number_format((float) $entry->pos_amount, 2) }}</td></tr><tr><td>Direct bank deposit</td><td>NGN {{ number_format((float) $entry->bank_deposit_amount, 2) }}</td></tr><tr><td><strong>Fuel revenue</strong></td><td><strong>NGN {{ number_format($detailFuel, 2) }}</strong></td></tr></tbody></table>
                    </div>
                    <div>
                        <h4 class="bm-detail-title">Expense items and amounts</h4>
                        @if ($entry->expenses->isEmpty())
                            <div class="bm-empty">No expense items recorded.</div>
                        @else
                            <table class="bm-mini-table"><thead><tr><th>Item description</th><th>Category</th><th>Amount</th></tr></thead><tbody>
                                @foreach ($entry->expenses as $expense)<tr><td>{{ $expense->description }}</td><td>{{ $expense->category }}</td><td>NGN {{ number_format((float) $expense->amount, 2) }}</td></tr>@endforeach
                                <tr><td colspan="2"><strong>Total expenses</strong></td><td><strong>NGN {{ number_format($detailExpenses, 2) }}</strong></td></tr>
                            </tbody></table>
                        @endif
                    </div>
                    <div>
                        <h4 class="bm-detail-title">Financial reconciliation</h4>
                        <table class="bm-mini-table"><tbody><tr><td>Actual net cash revenue</td><td>NGN {{ number_format($detailNetRevenue, 2) }}</td></tr><tr><td>Gross profit</td><td>NGN {{ number_format($detailGross, 2) }}</td></tr><tr><td>Net profit / loss</td><td class="{{ $detailNetProfit < 0 ? 'bm-loss' : '' }}">NGN {{ number_format($detailNetProfit, 2) }}</td></tr></tbody></table>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
    </div>
</x-filament-panels::page>
