<x-filament-widgets::widget>
    <style>
        .bm-workspace-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
        .bm-workspace-card { display: block; min-height: 142px; padding: 1.25rem; border: 1px solid #dbe3ee; border-radius: .75rem; background: #fff; color: #0f172a; transition: border-color .2s, box-shadow .2s, transform .2s; }
        .bm-workspace-card:hover { border-color: #2563eb; box-shadow: 0 8px 20px rgba(15, 23, 42, .08); transform: translateY(-2px); }
        .bm-workspace-card--primary { border-color: #bfdbfe; background: #eff6ff; }
        .bm-workspace-card__top { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; }
        .bm-workspace-card h3 { margin: 0; font-size: .95rem; font-weight: 700; }
        .bm-workspace-card p { margin: .5rem 0 0; color: #64748b; font-size: .82rem; line-height: 1.5; }
        .bm-workspace-card__icon { color: #2563eb; font-size: 1.2rem; }
        .bm-workspace-checklist { display: flex; flex-wrap: wrap; gap: .6rem 1.5rem; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; color: #64748b; font-size: .8rem; }
        .bm-workspace-checklist span { display: inline-flex; align-items: center; gap: .4rem; }
        .bm-workspace-checklist strong { color: #0f766e; font-weight: 700; }
        @media (max-width: 768px) { .bm-workspace-grid { grid-template-columns: 1fr; } }
    </style>

    <x-filament::section>
        <x-slot name="heading">Today's workspace</x-slot>
        <x-slot name="description">Keep station records current with the actions below.</x-slot>

        <div class="bm-workspace-grid">
            <a href="{{ url('/staff/daily-entries/create') }}" class="bm-workspace-card bm-workspace-card--primary">
                <div class="bm-workspace-card__top"><h3>Daily transaction</h3><x-filament::icon icon="heroicon-o-arrow-up-right" class="bm-workspace-card__icon" /></div>
                <p>Record pump readings, payments, and expenses.</p>
            </a>
            <a href="{{ url('/staff/stock-overview') }}" class="bm-workspace-card">
                <div class="bm-workspace-card__top"><h3>Review stock</h3><x-filament::icon icon="heroicon-o-arrow-up-right" class="bm-workspace-card__icon" /></div>
                <p>Check current tank levels before recording activity.</p>
            </a>
        </div>

        <div class="bm-workspace-checklist">
            <span><strong>OK</strong> Confirm readings before submitting</span>
            <span><strong>CALC</strong> Reconcile payments and expenses</span>
            <span><strong>ALERT</strong> Report delivery variances</span>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
