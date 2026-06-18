# Recu — Update

## Purpose

Allow editing a receipt's raw text after creation, and re-trigger extraction on save.

## Requirements

### Requirement: Edit receipt form

The system SHALL provide an edit form pre-filled with the receipt's `texte_brut`.

- Route: `GET /recus/{recu}/edit` → `RecuController::edit()`
- The view SHALL extend `x-app-layout` and render a textarea with the current `texte_brut`
- The view SHALL include a "Modifier" submit button
- The view SHALL include a cancel link back to `recus.show`

### Requirement: Update receipt

The system SHALL update the receipt's `texte_brut` and reset status to `en_attente`, then re-dispatch extraction.

- Route: `PUT /recus/{recu}` → `RecuController::update()`
- Authorization: `$this->authorize('update', $recu)` using existing `RecuPolicy`
- Validation SHALL use a new `UpdateRecuRequest` with the same rules as `StoreRecuRequest` (required, string, min:10, max:10000)
- On success: reset `statut` to `en_attente`, clear `payload_ia` and `message_erreur`, dispatch `ExtraireDepensesDuRecu`, redirect to `recus.show` with success flash
- Existing `depenses` SHALL be preserved (not deleted on update)

#### Scenario: Update receipt
- GIVEN an authenticated user owns a receipt
- WHEN they submit valid updated text
- THEN the receipt's `texte_brut` is updated, `statut` resets to `en_attente`, extraction is re-dispatched

#### Scenario: Update receipt (validation error)
- WHEN the submitted text is too short (<10 chars) or empty
- THEN the form re-displays with validation errors

### Requirement: Edit button in index

The system SHALL show an "Modifier" link in the receipt index table for each receipt.

### Requirement: Edit button on show page

The system SHALL show an "Modifier" link on the receipt detail page.

### Requirement: Tests

- Test `GET /recus/{recu}/edit` returns 200 for owner, 404 for other user
- Test `PUT /recus/{recu}` updates text and dispatches job
- Test `PUT /recus/{recu}` resets statut to `en_attente`
- Test `PUT /recus/{recu}` returns validation errors on bad data
- Test `PUT /recus/{recu}` returns 404 for other user
