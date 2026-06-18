# Recu — Retry Failed Extraction

## Purpose

Allow users to re-dispatch the AI extraction Job for receipts with `statut = echoue`.

## Requirements

### Requirement: Retry action on show page

The system SHALL provide a "Réessayer" button on the receipt detail page when `statut === 'echoue'`.

- The button SHALL be a form posting to `POST /recus/{recu}/retry`
- The button SHALL display "Réessayer l'extraction"
- The button SHALL be styled in blue (matching primary action style)
- Before retrying, the receipt SHALL reset: `statut = en_attente`, `payload_ia = null`, `message_erreur = null`, existing `depenses` deleted
- After reset, `ExtraireDepensesDuRecu` SHALL be dispatched
- On success, redirect back to `recus.show` with success flash: "L'extraction va être relancée."

### Requirement: Retry route and controller method

- Route: `POST /recus/{recu}/retry` → `RecuController::retry()`
- Authorization: scoped query `auth()->user()->recus()->findOrFail($recu->id)`
- Only SHALL proceed if `statut === 'echoue'`; otherwise redirect back with error flash

#### Scenario: Retry failed receipt
- GIVEN a receipt with `statut = echoue`
- WHEN the user clicks "Réessayer l'extraction"
- THEN the receipt's status resets to `en_attente`, expenses are deleted, Job is re-dispatched

#### Scenario: Retry non-failed receipt
- GIVEN a receipt with `statut != echoue`
- WHEN the user tries to retry (e.g., via direct POST)
- THEN the request is redirected back with an error flash

### Requirement: Tests

- Test `POST /recus/{recu}/retry` dispatches job for echoue receipt
- Test `POST /recus/{recu}/retry` resets status and deletes old expenses
- Test `POST /recus/{recu}/retry` returns error for non-echoue receipt
- Test `POST /recus/{recu}/retry` returns 404 for other user's receipt
