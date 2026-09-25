# Bestman Merchandise Nig. Ltd. — Fuel & Tanker Delivery Tracking System

Full-stack app for automating daily fuel station record-keeping: a **Staff Portal** (frontend) and **Admin Dashboard** (backend management), built with React + Express + MySQL (Prisma).

## Project Structure

```
bestman/
  server/   Express REST API, Prisma schema, JWT auth
  client/   React (Vite) app — Staff Portal + Admin Dashboard
```

## Prerequisites

- Node.js 18+
- MySQL 8+ running locally (or a connection string to a hosted instance)

## 1. Backend Setup (`server/`)

```powershell
cd server
copy .env.example .env
# edit .env and set your MySQL password in DATABASE_URL / JWT_SECRET
npm install
npx prisma migrate dev --name init
npm run seed
npm run dev
```

API runs at `http://localhost:4000`. Health check: `GET /api/health`.

Seeded accounts:
- Admin: `admin` / `Admin@123`
- Staff: `staff1` / `Staff@123`

## 2. Frontend Setup (`client/`)

```powershell
cd client
npm install
npm run dev
```

App runs at `http://localhost:5173` (Vite dev server proxies `/api` to the backend on port 4000).

Drop the official logo file at `client/public/logo.png` — it's referenced by the login screen, sidebar branding, and browser favicon.

## 3. Feature Map

### Staff Portal
- **Login** — Admin-issued Name/Username/Password.
- **Stock Inventory View** — live tank levels, selling price, expected value.
- **Tanker Delivery Intake** — logs plate no., supplier, invoice, waybill vs. received liters, auto variance alert.
- **Daily Transaction Entry** (single page) — pump readings with auto liters/total + price-variance flag, payment breakdown with sum validation, expenses, and net cash reconciliation.

### Admin Dashboard
- **User Management** — create/deactivate Staff & Admin accounts.
- **Tanks, Pumps & Prices** — configure tanks/pumps, dipping correction, base selling price per product.
- **Tanker Deliveries** — confirm/reject pending deliveries, set buying price (updates stock + rolling cost), shortage alerts.
- **Daily Reports** — full daily entry detail (readings, payments, expenses), active price/delivery alerts.
- **Profit & Loss** — gross/net profit per day and totals over a date range, with losses highlighted in red.

## 4. Key Business Rules Implemented

- Delivery variance = Waybill Liters − Received Liters (flagged if non-zero).
- Stock increases only after Admin **confirms** a delivery (`New Stock = Old Stock + Received Liters`).
- Price variance flagged when staff-entered selling price > Admin base price.
- Payment breakdown (Cash + POS + Bank Deposit) must equal Fuel Grand Total before a daily entry can submit.
- Net Cash Revenue = Fuel Grand Total − Total Expenses.
- Gross Profit = (Selling Price − Buying Price) × Liters Sold; Net Profit = Gross Profit − Expenses; negative values render in red.
