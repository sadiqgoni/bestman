# Bestman Merchandise Nig. Ltd. — Fuel & Tanker Delivery Tracking System

A Laravel + Filament application for automating daily fuel station record-keeping: an **Admin
Dashboard** for station management and an **Staff Portal** for day-to-day data entry, both
served from the same codebase as separate Filament panels.

## Stack

- **Laravel 13** + **Filament 5** (two panels: `/admin` and `/staff`)
- **MySQL**
- **Tailwind CSS v4** + **Vite** for the panel theme

## Panels

- **Admin** (`/admin`) — user management, tanks/pumps/rate card configuration, tanker
  delivery confirm/reject workflow, daily entry review, profit & loss, reports and alerts.
- **Staff** (`/staff`) — stock overview, tanker delivery intake, and the daily transaction
  entry form (pump readings, payment breakdown, expenses).

## Setup

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate
```

Edit `.env` and point `DB_CONNECTION`/`DB_HOST`/`DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD` at
a MySQL database, then:

```bash
php artisan migrate --seed
npm run build   # or `npm run dev` while working on the panel theme
php artisan serve
```

Seeded accounts (see `database/seeders/DatabaseSeeder.php`):

- Admin: `admin@gmail.com` / `12345678`
- Staff: `staff@gmail.com` / `12345678`

## Key business rules

- Delivery variance = waybill litres − received litres, calculated automatically on the
  delivery form.
- Tank stock only increases once Admin **confirms** a pending delivery
  (`TankerDelivery::confirm()`); rejecting a delivery leaves stock untouched. Both actions are
  idempotent — a delivery can only be confirmed or rejected once.
- Confirming a delivery also updates the product's rolling buying price.
- Price variance is flagged when a staff-entered selling price exceeds the product's base
  price at the time of entry.
- Payment breakdown (cash + POS + bank deposit) is reconciled live against the fuel grand
  total on the daily entry form.
- Net cash revenue = fuel grand total − total expenses.
- Gross profit = (selling price − buying price) × litres sold, per pump reading; net profit
  subtracts expenses. Losses are highlighted in red throughout the admin reports.

## Tests

```bash
php artisan test
```
