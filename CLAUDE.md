# Bestman — Fuel & Tanker Delivery Tracking System

Laravel 13 + Filament 5 app for Bestman Merchandise Nig. Ltd., a fuel station operator.
Two Filament panels share one codebase: `/admin` (station management) and `/staff`
(day-to-day data entry). See `README.md` for setup and the business-rule summary.

## Structure

- `app/Filament/Resources/...` — admin panel resources (Users, Products, Tanks, Pumps,
  Tanker Deliveries, Daily Entries) plus `app/Filament/Pages` (Reports, Profit & Loss).
- `app/Filament/Staff/...` — staff panel resources/pages, mirroring the operational subset
  admin has (Tanker Deliveries, Daily Entries, Stock Overview).
- `app/Filament/Concerns/RedirectsToIndex.php` — shared trait used on every Create/Edit page
  so saving returns to the resource's index instead of staying on the edit form.
- `app/Models/TankerDelivery.php` — `confirm()`/`reject()` encapsulate the only path that
  moves tank stock; never mutate `current_stock_liters` directly outside of this.
- `resources/css/filament/panel-theme.css` — the orange brand theme, loaded via
  `->viteTheme()` in both panel providers.

## Conventions

- Every Filament form is a `Section`-per-concern layout with an icon + one-line description,
  not a flat field list.
- Tables use `->badge()` for status/enum columns and `->money('NGN')` for currency, never bare
  `->numeric()` on a naira amount.
- New resources need `navigationGroup`, `navigationIcon`, and `navigationSort` set explicitly
  — panels are grouped (`Operations`, `Inventory & Pricing`, `Access` on admin).
- Filament forbids icons on both a `NavigationGroup` and its child items at once — group-level
  icons are intentionally omitted here for that reason.

## Local setup

Seeded accounts (`database/seeders/DatabaseSeeder.php`): `admin@gmail.com` / `12345678`
(ADMIN) and `staff@gmail.com` / `12345678` (STAFF). `php artisan migrate --seed` is idempotent.
