## Why

The shop owner has no visibility into their spending patterns. The dashboard shows "You're logged in!" and the expense list has no date filtering or summary totals. Without a spending overview, the owner cannot track monthly costs or identify where money goes.

## What Changes

- Replace the generic dashboard with expense summary cards (monthly total, category breakdown, recent expenses, pending receipts)
- Add date range filter to the expense index page
- Create a category summary view showing totals per category for a selected period
- Add CSV export for the filtered expense list

## Capabilities

### New Capabilities
- `dashboard`: Expense dashboard with summary statistics (monthly total, category breakdown, recent activity, pending receipts count)
- `expense-filters`: Date range filtering (`date_from`, `date_to`) for the expense list, composing with existing category filter
- `expense-summary`: Category-wise expense summary page showing totals grouped by `CategorieDepense` for a date range
- `expense-export`: CSV export of the current filtered expense list

### Modified Capabilities
*(none — no existing spec-level requirements are changing)*

## Impact

- `routes/web.php`: new dashboard route, summary route, export route
- `app/Http/Controllers/DepensesController.php`: enhanced `index()` with date filters, new `summary()` and `export()` methods
- `app/Http/Controllers/DashboardController.php`: new controller (or inline in dashboard route)
- `resources/views/dashboard.blade.php`: complete rewrite with summary cards
- `resources/views/depenses/index.blade.php`: add date range inputs
- `resources/views/depenses/summary.blade.php`: new category summary view
- Tests: new feature tests for filtering, summary, export, dashboard
