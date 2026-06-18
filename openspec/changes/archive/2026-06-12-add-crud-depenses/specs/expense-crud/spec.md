## ADDED Requirements

### Requirement: List all user expenses

The system SHALL provide a paginated list of the authenticated user's expenses with category filter and receipt context.

- Expenses SHALL be ordered by most recent first
- Each row SHALL show: libelle, quantite, prix_unitaire, total, categorie (with label), receipt date, action buttons (edit, delete)
- The view SHALL support filtering by category using a dropdown
- The view SHALL use eager loading: `auth()->user()->recus()->with('depenses')->...`
- The list SHALL be accessible at `GET /depenses`

#### Scenario: Visit expense list
- **WHEN** an authenticated user visits `/depenses`
- **THEN** they SHALL see only expenses linked to their own receipts

#### Scenario: Filter expenses by category
- **WHEN** a user selects "Alimentaire" in the category filter
- **THEN** only expenses with `categorie = alimentaire` SHALL be displayed

#### Scenario: Unauthenticated access
- **WHEN** an unauthenticated user visits `/depenses`
- **THEN** they SHALL be redirected to login

### Requirement: Create expense manually

The system SHALL allow users to create a new expense linked to a receipt.

- The form SHALL have fields: recu_id (select with only user's receipts), libelle (text), quantite (integer, min:1), prix_unitaire (numeric, min:0), categorie (select with enum cases)
- Submission SHALL go to `POST /recus/{recu}/depenses` (nested) or `POST /depenses` (standalone with recu_id in body)
- Validation SHALL use `StoreDepenseRequest`
- On success, redirect back with success message
- The `recu_id` SHALL be validated to ensure it belongs to the authenticated user

#### Scenario: Create expense with valid data
- **WHEN** a user submits valid expense data for one of their receipts
- **THEN** the expense SHALL be created and the user SHALL be redirected with a success message

#### Scenario: Create expense with invalid data
- **WHEN** a user submits an expense with quantite = 0
- **THEN** validation errors SHALL be shown

#### Scenario: Create expense for another user's receipt
- **WHEN** a user tries to create an expense for a receipt they don't own
- **THEN** a 403 error SHALL be returned

### Requirement: Edit expense

The system SHALL allow users to edit an existing expense they own.

- The edit form SHALL be pre-filled with existing values
- Submission SHALL go to `PUT /depenses/{depense}`
- Validation SHALL use `UpdateDepenseRequest`
- The `recu_id` SHALL NOT be changeable after creation
- On success, redirect back with success message

#### Scenario: Edit own expense
- **WHEN** a user submits valid updated data for their expense
- **THEN** the expense SHALL be updated and a success message SHALL be shown

#### Scenario: Edit another user's expense
- **WHEN** a user tries to edit an expense belonging to another user
- **THEN** a 403 error SHALL be returned

### Requirement: Delete expense individually

The system SHALL allow users to delete an individual expense without deleting the parent receipt.

- The delete action SHALL use `DELETE /depenses/{depense}`
- Authorization SHALL be scoped to the authenticated user's expenses
- On success, redirect back with success message
- Deleting an expense SHALL NOT affect the parent receipt or other expenses

#### Scenario: Delete own expense
- **WHEN** a user deletes their own expense
- **THEN** only that expense SHALL be deleted; the receipt and other expenses SHALL remain

#### Scenario: Delete another user's expense
- **WHEN** a user tries to delete an expense belonging to another user
- **THEN** a 403 error SHALL be returned
