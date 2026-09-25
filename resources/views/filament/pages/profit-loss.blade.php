<x-filament-panels::page>
    <style>
        .bm-pl-shell { padding: 1.25rem; border-radius: 1rem; background: linear-gradient(145deg, #f4f8fc 0%, #eef4fa 100%); }
        .bm-pl-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .bm-pl-card { padding: 1.2rem; border: 1px solid #dbe3ee; border-radius: .75rem; background: linear-gradient(145deg,#fff,#f8fbff); box-shadow: 0 10px 24px rgba(15,23,42,.06); }
        .bm-pl-label { color: #64748b; font: 600 .72rem Arial, sans-serif; text-transform: uppercase; letter-spacing: .05em; }
        .bm-pl-value { margin-top: .45rem; color: #083b91; font: 700 1.45rem Georgia, serif; }
        .bm-pl-loss { color: #d5222a; }
        .bm-pl-table { width: 100%; border-collapse: collapse; font: .83rem Arial, sans-serif; }
        .bm-pl-table th, .bm-pl-table td { padding: .8rem .5rem; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .bm-pl-table th { color: #64748b; font-size: .72rem; text-transform: uppercase; }
        @media (max-width: 800px) { .bm-pl-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .bm-pl-table { min-width: 650px; } }
    </style>
    <div class="bm-pl-shell">
    <div class="bm-pl-grid">
        <div class="bm-pl-card"><div class="bm-pl-label">Fuel revenue</div><div class="bm-pl-value">NGN {{ number_format($totals['revenue'], 2) }}</div></div>
        <div class="bm-pl-card"><div class="bm-pl-label">Total expenses</div><div class="bm-pl-value">NGN {{ number_format($totals['expenses'], 2) }}</div></div>
        <div class="bm-pl-card"><div class="bm-pl-label">Gross profit</div><div class="bm-pl-value">NGN {{ number_format($totals['grossProfit'], 2) }}</div></div>
        <div class="bm-pl-card"><div class="bm-pl-label">Net profit / loss</div><div class="bm-pl-value {{ $totals['netProfit'] < 0 ? 'bm-pl-loss' : '' }}">NGN {{ number_format($totals['netProfit'], 2) }}</div></div>
    </div>
    @if ($totals['netProfit'] < 0)<div style="margin-bottom:1.5rem;padding:1rem;border-left:4px solid #d5222a;border-radius:.5rem;background:#fff1f2;color:#881337;font:.9rem Arial,sans-serif"><strong>Loss alert:</strong> Net profit is negative for the selected records.</div>@endif
    <x-filament::section heading="Profit and loss by day" description="Gross profit is based on actual selling price less buying price; net profit subtracts expenses.">
        <div style="overflow-x:auto"><table class="bm-pl-table"><thead><tr><th>Date</th><th>Revenue</th><th>Gross profit</th><th>Expenses</th><th>Net profit / loss</th></tr></thead><tbody>
            @forelse ($entries as $entry)<tr><td>{{ $entry->entry_date->format('d M Y') }}</td><td>NGN {{ number_format($entry->report_revenue, 2) }}</td><td>NGN {{ number_format($entry->report_gross_profit, 2) }}</td><td>NGN {{ number_format($entry->report_expenses, 2) }}</td><td class="{{ $entry->report_net_profit < 0 ? 'bm-pl-loss' : '' }}">NGN {{ number_format($entry->report_net_profit, 2) }}</td></tr>@empty<tr><td colspan="5">No profit and loss data is available yet.</td></tr>@endforelse
        </tbody></table></div>
    </x-filament::section>
    </div>
</x-filament-panels::page>
