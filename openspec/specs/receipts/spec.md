# Receipts

## Purpose

Manage receipts from supplier purchases — store raw text, track AI extraction status, validate input.

## Requirements

### Requirement: Receipt model with status tracking

The system SHALL provide a Recu model that stores raw receipt text and tracks processing status through a StatutRecu enum.

- Recu SHALL have a `belongsTo` relationship to User
- Recu SHALL have a `hasMany` relationship to Depense
- Recu SHALL cast `statut` to the `StatutRecu` enum
- Recu SHALL cast `payload_ia` as an array (nullable)
- Recu SHALL have `texte_brut`, `statut`, `payload_ia`, `message_erreur` as fillable attributes
- Recu SHALL cascade delete its Depenses when deleted
- The `statut` field SHALL default to `en_attente`
- On AI success, statut SHALL be updated to `traite` and `payload_ia` SHALL store the raw AI response
- On AI failure, statut SHALL be updated to `echoue` and `message_erreur` SHALL store a readable error message

#### Scenario: Create receipt with default status

- **WHEN** a new Recu is created with `texte_brut`
- **THEN** the `statut` SHALL be `en_attente`

#### Scenario: Mark receipt as processed

- **WHEN** `statut` is updated to `traite` and `payload_ia` is set
- **THEN** the receipt SHALL be considered processed

#### Scenario: Mark receipt as failed

- **WHEN** `statut` is updated to `echoue` and `message_erreur` is set
- **THEN** the receipt SHALL be considered failed with a readable error

### Requirement: StatutRecu enum with labels

The system SHALL provide a `StatutRecu` backed string enum with cases `en_attente`, `traite`, and `echoue`, each with a French `label()` method.

#### Scenario: Label display

- **WHEN** `StatutRecu::en_attente->label()` is called
- **THEN** it SHALL return "En attente"

### Requirement: Receipt text validation

The system SHALL validate receipt text via `StoreRecuRequest` form request.

- `texte_brut` SHALL be required, string, min:10 characters, max:10000 characters

#### Scenario: Valid receipt text

- **WHEN** `texte_brut` is 10+ characters and at most 10000 characters
- **THEN** validation SHALL pass

#### Scenario: Receipt text too short

- **WHEN** `texte_brut` is fewer than 10 characters
- **THEN** validation SHALL fail with an error on `texte_brut`

### Requirement: Extraction lifecycle via Job

The system SHALL transition receipt status through the complete extraction lifecycle via the queued `ExtraireDepensesDuRecu` Job.

- `en_attente` → Job created and queued
- `traite` → Job completed successfully, expenses stored in database
- `echoue` → Job failed, error message stored in `message_erreur`
- The Job SHALL be the only mechanism for transitioning out of `en_attente`

#### Scenario: Lifecycle en_attente to traite
- **WHEN** the `ExtraireDepensesDuRecu` Job runs successfully
- **THEN** status transitions from `en_attente` to `traite`

#### Scenario: Lifecycle en_attente to echoue
- **WHEN** the `ExtraireDepensesDuRecu` Job fails
- **THEN** status transitions from `en_attente` to `echoue`
