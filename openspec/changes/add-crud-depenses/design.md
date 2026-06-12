## Context

The app currently supports expenses as read-only line items extracted via AI from receipts. Users cannot manually create, edit, or delete individual expenses. All CRUD operations exist only at the receipt level (create receipt with AI extraction, view expenses as a table, delete entire receipt with cascade). This design adds full manual expense CRUD while preserving the existing AI extraction flow.

## Goals / Non-Goals

**Goals:**
- Users can manually add expenses to any of their receipts
- Users can edit existing expense fields (libelle, quantite, prix_unitaire, categorie)
- Users can delete individual expenses without affecting the parent receipt
- Users can browse all their expenses in a single paginated list with category filtering
- Authorization is enforced for every expense operation via policy
- Inline add/edit/delete actions on the receipt detail view

**Non-Goals:**
- Batch operations (multi-select edit/delete)
- Bulk import of expenses
- Expense reporting or charts
- Modifying recu_id after creation (expense stays linked to original receipt)

## Decisions

### Decision: Nested + flat routes

Expenses will be accessible via two route patterns:
- `GET /depenses` — global list of all user expenses
- `POST /recus/{recu}/depenses` — nested creation (used from receipt detail view)
- `GET|PUT /depenses/{depense}` — edit/update by ID
- `DELETE /depenses/{depense}` — delete by ID

**Rationale**: The nested route enforces receipt ownership at the URL level for creation. Flat routes are simpler for edit/delete since the expense already has an ID and belongs to a receipt.

### Decision: Single DepenseController

A single `DepenseController` will handle all CRUD actions with explicit route model binding. The `recu` parameter for nested routes will be resolved separately.

**Rationale**: Keeps related logic together. Avoids splitting CRUD across multiple controllers for what is still a single resource.

### Decision: DepensePolicy for authorization

A `DepensePolicy` will check `$user->id === $depense->recu->user_id` for view/update/delete actions.

**Rationale**: The policy pattern matches the existing architecture and is testable via Pest. Using `$depense->recu->user_id` avoids needing a direct `user_id` on the depenses table.

### Decision: Inline forms on receipt detail view

The receipt detail view will use inline forms (with `_method` spoofing for PUT/DELETE) rather than a separate SPA or modal library.

**Rationale**: Keeps the implementation simple with plain Blade + JavaScript. No additional frontend dependencies. The same controller endpoints support both inline and standalone use.

### Decision: Category filter via query string

The `/depenses` list will filter by `?categorie=alimentaire` using a query scope on the relationship.

**Rationale**: Stateless, bookmarkable, works with standard Blade pagination. No need for Livewire or Alpine for this simple filter.

## Risks / Trade-offs

- [Risk] Inline edit forms increase complexity in the Blade view
  → Mitigation: Use simple JavaScript toggle between display and edit mode; fall back to separate edit page if inline becomes unwieldy
- [Risk] Cascade delete from Recu could orphan expense edit links
  → Mitigation: Already handled by existing cascade; navigating to a deleted expense returns 404 naturally
