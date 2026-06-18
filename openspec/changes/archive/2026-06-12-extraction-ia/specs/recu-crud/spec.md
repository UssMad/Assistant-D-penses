## ADDED Requirements

### Requirement: Dispatch extraction Job on receipt creation

The system SHALL dispatch the `ExtraireDepensesDuRecu` Job after a receipt is successfully created and saved.

- The dispatch SHALL happen in the controller's `store` method after the receipt is created
- The Job SHALL be dispatched with the receipt ID as a parameter
- The dispatch SHALL NOT block the HTTP response (fire-and-forget via queue)

#### Scenario: Job dispatched after store
- **WHEN** a user submits a valid receipt form
- **THEN** the receipt SHALL be created with `statut = en_attente`, the Job SHALL be dispatched, and the user SHALL be redirected immediately

### Requirement: Refresh pending receipt status

The system SHALL allow users to manually refresh a receipt detail page to check if extraction has completed.

- A "Refresh" button SHALL appear when the receipt status is `en_attente`
- Clicking "Refresh" SHALL reload the page and re-check the status
- When status becomes `traite`, the expense table SHALL appear
- When status becomes `echoue`, the error message SHALL appear

#### Scenario: Refresh pending receipt
- **WHEN** a user views a receipt with `statut = en_attente` and clicks "Refresh"
- **THEN** the page SHALL reload and show the updated status
