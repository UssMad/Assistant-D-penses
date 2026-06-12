## Context

The project currently has stub `Recu` and `Depenses` models with no relationships, casts, or fillable attributes. The `recus` migration exists but is missing `message_erreur`. The `depenses` migration has a generic schema (user_id, montant, devise, date_achat, description) that doesn't match the AI extraction contract's structured output. No enums, policies, or form requests exist yet.

## Goals / Non-Goals

**Goals:**
- Create `StatutRecu` and `CategorieDepense` enums with `label()` methods and Eloquent casts
- Flesh out `Recu` model with `belongsTo User`, `hasMany Depense`, enum cast for statut, array cast for payload, fillable attributes
- Flesh out `Depense` model with `belongsTo Recu`, `belongsTo User` (through Recu), enum cast for categorie, fillable attributes
- Add `hasMany Recu` relationship on User
- Add a migration to add `message_erreur` to recus table
- Restructure depenses table to match spec (recu_id, libelle, quantite, prix_unitaire, categorie enum)
- Create `StoreRecuRequest` with `texte_brut` validation (required, string, min:10, max:10000)
- Create `RecuPolicy` scoping all queries to authenticated user
- Cascade delete: deleting a Recu deletes its Depenses

**Non-Goals:**
- No controllers, routes, or views
- No AI extraction logic or Job classes
- No receipt listing or dashboard UI

## Decisions

### 1. Enum implementation: Native PHP enums with Eloquent cast

Eloquent supports casting to native PHP enums via `\Illuminate\Contracts\Database\Eloquent\Castable`. Using native enums gives us type safety, IDE autocompletion, and the `label()` method for display.

Alternatives considered: JSON columns with string values (no type safety), database-level enums (harder to migrate).

### 2. Depenses table restructure: New migration, drop old table

The current `depenses` table has no production data. Rather than writing a complex migration, create a fresh migration that drops the old table and recreates it with the proper schema (`recu_id`, `libelle`, `quantite`, `prix_unitaire`, `categorie` enum). `recu_id` references `recus.id` with `onDelete('cascade')`.

### 3. Authorization: Policy over middleware

A `RecuPolicy` with `view`, `create`, `update`, `delete` methods that all check `$user->id === $recu->user_id`. For listing, controllers will scope via `auth()->user()->recus()`. This avoids trusting route model binding alone.

### 4. Eager loading strategy

- Recu listing: `auth()->user()->recus()->with('depenses')` to avoid N+1
- Single Recu view: `Recu::with('depenses')->findOrFail($id)` with ownership check
- Use `withCount('depenses')` if displaying expense count on index

## Risks / Trade-offs

- **[Risk] Dropping old depenses table** → Mitigation: ensure no production data exists; if data exists, write a proper data migration instead
- **[Risk] Enum values in migration vs code** → Mitigation: define the enum cases in the PHP enum and reference them by value in the migration (string values, not DB enums, for portability)
- **[Risk] N+1 on depenses** → Mitigation: always eager-load depenses via `with()` in controllers; verify with Laravel Debugbar
