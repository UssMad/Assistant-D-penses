## Why

Users need a web UI to submit receipts, view extraction status, browse extracted expenses, and delete receipts. Currently only the domain layer (models, enums, policy) exists — no controllers, routes, or views.

## What Changes

- Implement `RecuController` with full CRUD: index (list), create (form), store (validate + save + dispatch job), show (receipt + expenses), destroy (delete with cascade)
- Implement `DepensesController` read-only: index (list expenses from user's receipts), show (single expense)
- Create Blade views for receipt listing, creation form, detail view, and expense listing
- Add routes under `routes/web.php` grouped by auth
- Wire `StoreRecuRequest` into the store method
- Apply `RecuPolicy` authorization on all actions
- Use eager loading to prevent N+1

## Capabilities

### New Capabilities

- `recu-crud`: Receipt CRUD — browse, create, view, delete receipts with status-aware UI

### Modified Capabilities

- `authorization`: Route-level authorization — policy gates applied to all receipt/expense controllers

## Impact

- `app/Http/Controllers/RecuController.php` — implement all 7 resource methods
- `app/Http/Controllers/DepensesController.php` — implement index + show
- `routes/web.php` — add resource routes + expense routes
- `resources/views/recus/` — new directory with index, create, show views
- `resources/views/depenses/` — new directory with index, show views
- `resources/views/layouts/` — may need navigation link updates
