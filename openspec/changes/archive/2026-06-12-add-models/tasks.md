## 1. Enums

- [x] 1.1 Create `StatutRecu` backed string enum with cases `en_attente`, `traite`, `echoue` and French `label()` method
- [x] 1.2 Create `CategorieDepense` backed string enum with cases `alimentaire`, `boissons`, `hygiene`, `entretien`, `autre` and French `label()` method
- [x] 1.3 Write enum cast and label tests

## 2. Migrations

- [x] 2.1 Create migration to add nullable `message_erreur` (text) column to `recus` table
- [x] 2.2 Create migration to drop existing `depenses` table and recreate with `recu_id`, `libelle` (string), `quantite` (integer), `prix_unitaire` (decimal 10,2), `categorie` (string enum values), `timestamps`; `recu_id` foreign cascades on delete; also add `user_id` foreign via recu relationship
- [x] 2.3 Run migrations and verify schema

## 3. Models

- [x] 3.1 Flesh out `Recu` model: fillable (`texte_brut`, `statut`, `payload_ia`, `message_erreur`), `belongsTo User`, `hasMany Depense`, casts (`statut` → `StatutRecu`, `payload_ia` → `array`)
- [x] 3.2 Flesh out `Depense` model: fillable (`recu_id`, `libelle`, `quantite`, `prix_unitaire`, `categorie`), `belongsTo Recu`, cast (`categorie` → `CategorieDepense`)
- [x] 3.3 Add `recus()` hasMany and `depenses()` hasManyThrough relationships to `User` model

## 4. Form Request

- [x] 4.1 Create `StoreRecuRequest` with `texte_brut` validation: required, string, min:10, max:10000
- [x] 4.2 Write form request validation tests

## 5. Policy

- [x] 5.1 Create `RecuPolicy` with `view`, `create`, `update`, `delete` methods scoped to `$user->id === $recu->user_id`
- [x] 5.2 Register policy in `AuthServiceProvider`
- [x] 5.3 Write policy authorization tests

## 6. Tests

- [x] 6.1 Test Recu model: creation, relationships, enum casts, cascade delete
- [x] 6.2 Test Depense model: creation, relationships, enum cast
- [x] 6.3 Test User model: `recus()` and `depenses()` relationships
