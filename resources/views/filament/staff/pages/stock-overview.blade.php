<x-filament-panels::page>
    @php
        $totalStock = $tanks->sum(fn ($tank) => (float) $tank->current_stock_liters);
        $totalCapacity = $tanks->sum(fn ($tank) => (float) $tank->capacity_liters);
    @endphp

    <style>
        .bm-stock-summary { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .bm-stock-summary-card { padding: 1.25rem; border: 1px solid #dbe3ee; border-radius: .75rem; background: #fff; }
        .bm-stock-summary-label { color: #64748b; font: 600 .76rem Arial, sans-serif; text-transform: uppercase; letter-spacing: .06em; }
        .bm-stock-summary-value { margin-top: .4rem; color: #0f172a; font: 700 1.65rem Georgia, serif; }
        .bm-stock-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
        .bm-stock-card { padding: 1.25rem; border: 1px solid #dbe3ee; border-radius: .75rem; background: #fff; }
        .bm-stock-card__header { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; }
        .bm-stock-card h3 { margin: 0; color: #0f172a; font: 700 1.05rem Georgia, serif; }
        .bm-stock-product { margin-top: .3rem; color: #64748b; font: 700 .74rem Arial, sans-serif; letter-spacing: .08em; text-transform: uppercase; }
        .bm-stock-litres { color: #083b91; font: 700 1.5rem Georgia, serif; white-space: nowrap; }
        .bm-stock-litres span { color: #64748b; font: 400 .78rem Arial, sans-serif; }
        .bm-stock-track { height: 9px; margin: 1.15rem 0 .5rem; overflow: hidden; border-radius: 99px; background: #e8eef6; }
        .bm-stock-fill { height: 100%; border-radius: inherit; background: #083b91; }
        .bm-stock-meta { display: flex; justify-content: space-between; color: #64748b; font: .78rem Arial, sans-serif; }
        .bm-stock-details { display: grid; grid-template-columns: 1fr; gap: .75rem; margin-top: 1.2rem; padding-top: 1rem; border-top: 1px solid #e8eef6; }
        .bm-stock-detail-label { color: #64748b; font: .72rem Arial, sans-serif; }
        .bm-stock-detail-value { margin-top: .25rem; color: #172554; font: 700 .9rem Arial, sans-serif; }
        .bm-stock-empty { padding: 3rem 1rem; border: 1px dashed #cbd5e1; border-radius: .75rem; color: #64748b; text-align: center; font: 15px Arial, sans-serif; }
        @media (max-width: 800px) { .bm-stock-summary, .bm-stock-grid { grid-template-columns: 1fr; } }
        @media (max-width: 480px) { .bm-stock-details { grid-template-columns: 1fr; } }
    </style>

    <div class="bm-stock-summary">
        <div class="bm-stock-summary-card"><div class="bm-stock-summary-label">Total available stock</div><div class="bm-stock-summary-value">{{ number_format($totalStock, 0) }} L</div></div>
        <div class="bm-stock-summary-card"><div class="bm-stock-summary-label">Overall tank capacity</div><div class="bm-stock-summary-value">{{ number_format($totalCapacity, 0) }} L</div></div>
    </div>

    <x-filament::section heading="Live tank inventory" description="Current quantities and active product prices configured by Admin.">
        @if ($tanks->isEmpty())
            <div class="bm-stock-empty">No active tanks have been configured yet. Please contact your System Admin.</div>
        @else
            <div class="bm-stock-grid">
                @foreach ($tanks as $tank)
                    @php
                        $stock = (float) $tank->current_stock_liters;
                        $capacity = (float) $tank->capacity_liters;
                        $fill = $capacity > 0 ? min(100, ($stock / $capacity) * 100) : 0;
                    @endphp
                    <article class="bm-stock-card">
                        <div class="bm-stock-card__header"><div><h3>{{ $tank->name }}</h3><div class="bm-stock-product">{{ $tank->product->type }}</div></div><div class="bm-stock-litres">{{ number_format($stock, 0) }} <span>L</span></div></div>
                        <div class="bm-stock-track" aria-label="{{ number_format($fill, 0) }} percent full"><div class="bm-stock-fill" style="width: {{ $fill }}%"></div></div>
                        <div class="bm-stock-meta"><span>{{ number_format($fill, 0) }}% full</span><span>Capacity {{ number_format($capacity, 0) }} L</span></div>
                        <div class="bm-stock-details"><div><div class="bm-stock-detail-label">Active selling price</div><div class="bm-stock-detail-value">NGN {{ number_format((float) $tank->product->base_price_per_liter, 2) }}/L</div></div></div>
                    </article>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
