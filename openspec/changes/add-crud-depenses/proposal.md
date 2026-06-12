## Why

Currently, expenses can only be created via AI extraction from receipt text. The shop owner needs the ability to manually add, edit, and delete individual expense line items — either to correct AI extraction errors or to record expenses without going through a receipt paste.

## What Changes

- Add manual expense creation form (standalone and per-receipt)
- Add expense edit form with pre-filled fields
- Add individual expense deletion with confirmation
- Add a global expense listing page with category filter and receipt context
- Add authorization policy for Depense model
- Modify the receipt detail view to include "Add expense" and inline edit/delete actions on each expense row

## Capabilities

### New Capabilities
- `expense-crud`: Full manual CRUD for expense line items — create, read, update, delete — with category filtering and receipt association

### Modified Capabilities
- `expenses`: Update expense spec to cover manual CRUD requirements alongside AI-generated expenses
- `recu-crud`: Update receipt detail view to expose inline expense actions (add, edit, delete)

## Impact

- New controller `DepenseController` with standard CRUD methods
- New Form Request `StoreDepenseRequest`, `UpdateDepenseRequest`
- New `DepensePolicy` for authorization
- New Blade views: `depenses/index`, `depenses/create`, `depenses/edit`, and partials for inline use
- Modified `recus/show` view to include expense action buttons
- Updated routes: nested under `recus/{recu}/depenses/*` and global `/depenses/*`
- Tests for all CRUD operations, auth isolation, and validation
