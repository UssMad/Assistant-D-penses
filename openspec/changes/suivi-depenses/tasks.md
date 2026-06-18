## 1. Dashboard

- [x] 1.1 Create `DashboardController` with `__invoke()` method computing four summary cards (monthly total, category breakdown, recent 5 expenses, pending receipts count)
- [x] 2.1 Replace dashboard route (`/dashboard`) to use `DashboardController` in `routes/web.php`
- [x] 3.1 Rewrite `resources/views/dashboard.blade.php` with four summary cards in a grid layout

## 2. Expense Date Filters

- [x] 2.1 Add `date_from` and `date_to` query params to `DepensesController::index()` with `->when()` clauses and `->appends()`
- [x] 2.2 Add date input fields and clear link to `resources/views/depenses/index.blade.php` filter section

## 3. Category Summary

- [x] 3.1 Add `summary()` method to `DepensesController` with grouped aggregate query and optional date filter
- [x] 3.2 Add `GET /depenses/summary` route
- [x] 3.3 Create `resources/views/depenses/summary.blade.php` with category table and date filter
- [x] 3.4 Add "Résumé par catégorie" nav link to the expense index page

## 4. CSV Export

- [x] 4.1 Add `export()` method to `DepensesController` returning a streamed CSV download, respecting existing filters
- [x] 4.2 Add `GET /depenses/export` route
- [x] 4.3 Add "Exporter en CSV" button to `resources/views/depenses/index.blade.php`

## 5. Tests

- [x] 5.1 Test dashboard: renders for auth user, blocked for guest, correct monthly total, correct pending count
- [x] 5.2 Test date filter: `date_from`, `date_to`, combined with category, invalid dates ignored
- [x] 5.3 Test summary: correct totals, filtered summary, empty state, data isolation
- [x] 5.4 Test export: returns CSV with headers, respects filters, blocked for guest
