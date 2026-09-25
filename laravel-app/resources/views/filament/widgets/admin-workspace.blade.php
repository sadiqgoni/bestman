<x-filament-widgets::widget>
    <style>
        .bm-workspace-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
        .bm-workspace-card { display: block; min-height: 142px; padding: 1.25rem; border: 1px solid #f3d9c4; border-radius: .75rem; background: #fff; color: #0f172a; transition: border-color .2s, box-shadow .2s, transform .2s; }
        .bm-workspace-card:hover { border-color: #c2410c; box-shadow: 0 8px 20px rgba(120, 53, 15, .1); transform: translateY(-2px); }
        .bm-workspace-card--primary { border-color: #fdba8c; background: #fff7ed; }
        .bm-workspace-card__top { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; }
        .bm-workspace-card h3 { margin: 0; font-size: .95rem; font-weight: 700; }
        .bm-workspace-card p { margin: .5rem 0 0; color: #64748b; font-size: .82rem; line-height: 1.5; }
        .bm-workspace-card__icon { color: #c2410c; font-size: 1.2rem; }
        .bm-workspace-checklist { display: flex; flex-wrap: wrap; gap: .6rem 1.5rem; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; color: #64748b; font-size: .8rem; }
        .bm-workspace-checklist span { display: inline-flex; align-items: center; gap: .4rem; }
        .bm-workspace-checklist strong { color: #c2410c; font-weight: 700; }
        @media (max-width: 900px) { .bm-workspace-grid { grid-template-columns: 1fr; } }
    </style>

    <x-filament::section>
        <x-slot name="heading">Admin workspace</x-slot>
        <x-slot name="description">Jump straight into the things that need attention today.</x-slot>

        <div class="bm-workspace-grid">
            <a href="{{ url('/admin/tanker-deliveries') }}" class="bm-workspace-card bm-workspace-card--primary">
                <div class="bm-workspace-card__top"><h3>Review deliveries</h3><x-filament::icon icon="heroicon-o-arrow-up-right" class="bm-workspace-card__icon" /></div>
                <p>Confirm or reject pending tanker deliveries and update stock.</p>
            </a>
            <a href="{{ url('/admin/reports') }}" class="bm-workspace-card">
                <div class="bm-workspace-card__top"><h3>Reports & alerts</h3><x-filament::icon icon="heroicon-o-arrow-up-right" class="bm-workspace-card__icon" /></div>
                <p>Price variances, delivery shortages, and pending reviews.</p>
            </a>
            <a href="{{ url('/admin/profit-loss') }}" class="bm-workspace-card">
                <div class="bm-workspace-card__top"><h3>Profit & loss</h3><x-filament::icon icon="heroicon-o-arrow-up-right" class="bm-workspace-card__icon" /></div>
                <p>Daily gross and net profit across all stations.</p>
            </a>
        </div>

        <div class="bm-workspace-checklist">
            <span><strong>{{ $priceAlerts }}</strong> price variance{{ $priceAlerts === 1 ? '' : 's' }} to review</span>
            <span><strong>{{ $pendingDeliveries }}</strong> deliver{{ $pendingDeliveries === 1 ? 'y' : 'ies' }} awaiting confirmation</span>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
