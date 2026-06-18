## Context

Controllers exist as stubs with empty methods. No routes or views exist for receipts/expenses. The domain layer (models, enums, policy, form request) is already in place from the `add-models` change.

## Goals / Non-Goals

**Goals:**
- Implement all 7 resource methods in `RecuController` (index, create, store, show, edit, update, destroy)
- Implement `DepensesController` read-only (index, show)
- Create Blade views: receipt index, receipt create form, receipt show (with expenses), expense list, expense detail
- Add resource routes for `/recus` and routes for `/depenses`
- Apply `RecuPolicy` via `$this->authorize()` in all controller methods
- Use `StoreRecuRequest` in the store method
- Use eager loading (`with('depenses')`) for receipt detail

**Non-Goals:**
- No AI extraction logic or Job dispatching — that's a separate change
- No expense create/edit/delete — expenses are read-only, created by AI
- No dashboard or statistics

## Decisions

### 1. Controller authorization via $this->authorize()

Each controller method calls `$this->authorize('action', Recu::class)` or `$this->authorize('action', $recu)` using the existing `RecuPolicy`. For listing, the controller scopes to `auth()->user()->recus()` directly.

### 2. Eager loading strategy

- Receipt index: `auth()->user()->recus()->withCount('depenses')->latest()->get()`
- Receipt show: `auth()->user()->recus()->with('depenses')->findOrFail($id)`
- Expense index: `Depenses::whereHas('recu', fn($q) => $q->where('user_id', auth()->id()))->with('recu')->latest()->get()`

### 3. Blade view structure

- `resources/views/recus/index.blade.php` — table with status badges, "Créer" button
- `resources/views/recus/create.blade.php` — form with texte_brut textarea
- `resources/views/recus/show.blade.php` — receipt detail + expense table
- `resources/views/depenses/index.blade.php` — expense table with category badges
- `resources/views/depenses/show.blade.php` — single expense detail

### 4. Status-aware UI

StatutRecu labels used in badges: `en_attente` → yellow, `traite` → green, `echoue` → red.

## Risks / Trade-offs

- **[Risk] Route model binding without ownership** → Mitigation: use scoped queries (`auth()->user()->recus()->findOrFail($id)`) instead of relying on implicit binding
- **[Risk] N+1 on expense lists** → Mitigation: always eager-load relationships; verify with Debugbar
