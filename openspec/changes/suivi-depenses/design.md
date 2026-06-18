## Context

The app currently has a generic dashboard ("You're logged in!"), an expense list with only category filtering, and no summary/export features. This change adds spending visibility through a dashboard, enhanced filters, category summary, and CSV export.

## Goals / Non-Goals

**Goals:**
- Replace the generic dashboard with expense summary cards
- Add date range filtering to expense index
- Create a category-wise summary page
- Add CSV export for expenses

**Non-Goals:**
- No charting library or visual graphs (plain HTML/CSS cards)
- No PDF export
- No email reports
- No recurring expense tracking

## Decisions

### 1. Dashboard query strategy

Compute all four cards in a single controller method using aggregate queries:
- Monthly total: `Depenses::whereHas('recu', ...)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->get()->sum(fn($d) => $d->quantite * $d->prix_unitaire)`
- Category breakdown: same scope plus `->selectRaw('categorie, sum(quantite * prix_unitaire) as total')->groupBy('categorie')->pluck('total', 'categorie')`
- Recent: same scope plus `->latest()->take(5)->get()`
- Pending count: `auth()->user()->recus()->where('statut', 'en_attente')->count()`

No new DB queries — each card is one query.

### 2. Dashboard controller

New `DashboardController` with a single `__invoke()` method, wired to `Route::get('/dashboard', ...)`. Keeps separation of concerns.

### 3. Expense date filter

Add `date_from` and `date_to` query params in `DepensesController::index()`, composing with existing `categorie` filter via `->when()`. Uses `whereDate('created_at', '>=', $v)` for inclusiveness.

### 4. Category summary

New `DepensesController::summary()` method using a grouped aggregate query. No new model or DB structure needed.

### 5. CSV export

New `DepensesController::export()` returning a streamed response:
```php
return response()->streamDownload(function () { ... }, $filename, ['Content-Type' => 'text/csv']);
```
Write CSV rows inline with `fputcsv()` to avoid memory issues. Respects same filters as index.

## Routes

| Method | URI | Controller | Auth | Scoped |
|--------|-----|-----------|------|--------|
| GET | `/dashboard` | `DashboardController` | `auth` middleware | `auth()->user()` |
| GET | `/depenses/summary` | `DepensesController::summary()` | `auth` middleware | `whereHas('recu', user_id)` |
| GET | `/depenses/export` | `DepensesController::export()` | `auth` middleware | `whereHas('recu', user_id)` |

## Eager Loading Strategy

- Dashboard: no relationships needed (aggregate queries only)
- Expense index: `with('recu')` — no change from current
- Summary: no relationships needed (grouped aggregates)
- Export: `with('recu')` for date context

## Risks / Trade-offs

- **[Risk] Dashboard performance with many expenses** → Mitigation: aggregate queries run at DB level, not in PHP. All four queries are lightweight.
- **[Risk] CSV export blocks request on large datasets** → Mitigation: use `streamDownload` + `fputcsv` for streaming; if dataset grows, add chunking.
