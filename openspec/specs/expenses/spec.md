# Expenses

## Purpose

Store individual expense line items extracted from receipts, categorized by type (alimentaire, boissons, hygiene, entretien, autre).

## Requirements

### Requirement: Expense model with category

The system SHALL provide a Depense model that stores individual expense line items extracted from a receipt.

- Depense SHALL have a `belongsTo` relationship to Recu
- Depense SHALL have a `belongsTo` relationship to User (through Recu)
- Depense SHALL cast `categorie` to the `CategorieDepense` enum
- Depense SHALL have `recu_id`, `libelle`, `quantite`, `prix_unitaire`, `categorie` as fillable attributes
- Depense SHALL belong to a Recu; deleting the Recu SHALL cascade delete its Depenses

#### Scenario: Create expense for receipt

- **WHEN** a new Depense is created with `recu_id`, `libelle`, `quantite`, `prix_unitaire`, `categorie`
- **THEN** it SHALL be retrievable via `Recu::with('depenses')`

#### Scenario: Cascade delete

- **WHEN** a Recu is deleted
- **THEN** all its associated Depenses SHALL also be deleted

### Requirement: CategorieDepense enum with labels

The system SHALL provide a `CategorieDepense` backed string enum with cases `alimentaire`, `boissons`, `hygiene`, `entretien`, and `autre`, each with a French `label()` method.

#### Scenario: Label display for hygiene

- **WHEN** `CategorieDepense::hygiene->label()` is called
- **THEN** it SHALL return "Hygiène"

#### Scenario: Label display for food

- **WHEN** `CategorieDepense::alimentaire->label()` is called
- **THEN** it SHALL return "Alimentaire"
