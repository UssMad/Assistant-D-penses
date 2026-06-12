## Why

The Recu and Depense models exist as stubs with no relationships, casts, or fillable attributes. Migrations don't match the spec (missing `message_erreur` on recus, depenses table has wrong schema). Without proper models, the receipt → expense extraction flow cannot be built.

## What Changes

- Create `StatutRecu` and `CategorieDepense` enums with Eloquent casts
- Flesh out `Recu` model with relationships, casts (`StatutRecu`, `array` for payload), fillable
- Flesh out `Depense` model with relationships, casts (`CategorieDepense`), fillable
- Update `User` model with `hasMany Recu` relationship
- Add migration: `message_erreur` column to recus table
- Add migration: restructure depenses table to match spec (recu_id, libelle, quantite, prix_unitaire, categorie enum)
- Create `StoreRecuRequest` form request for receipt validation
- Create `RecuPolicy` for user data isolation
- Seed initial category data if needed

## Capabilities

### New Capabilities

- `receipts`: Receipt model, enums, validation, and CRUD foundations
- `expenses`: Expense model with proper schema and enum categorization
- `authorization`: User-scoped data isolation via policies

### Modified Capabilities

<!-- No existing specs to modify -->

## User Stories

- As a shop owner, I want receipts to have a clear status (pending/processed/failed) so I know what's been extracted
- As a shop owner, I want each expense to have a category so I can filter spending later
- As a shop owner, I want my receipts and expenses isolated from other users

## Non-goals

- No UI or controllers — this change is pure domain layer
- No AI extraction logic — that comes after models are ready
- No receipt listing or dashboard — separate change

## Impact

- `app/Models/Recu.php` — rewritten with relationships, casts, fillable
- `app/Models/Depenses.php` — rewritten with relationships, casts, fillable
- `app/Models/User.php` — add `recus()` relationship
- `app/Enums/StatutRecu.php` — new file
- `app/Enums/CategorieDepense.php` — new file
- `app/Http/Requests/StoreRecuRequest.php` — new file
- `app/Policies/RecuPolicy.php` — new file
- New migration files in `database/migrations/`
- Existing migration may need modification for depenses table restructure
