# Recu CRUD

## Purpose

Provide web UI for managing receipts — list, create, view, and delete receipts with status-aware display and expense browsing.

## Requirements

### Requirement: List user receipts

The system SHALL display a paginated list of the authenticated user's receipts with status badges and expense counts.

- Receipts SHALL be ordered by most recent first
- Each row SHALL show: date, status (with badge), number of expenses, action buttons (view, delete)
- The view SHALL use `auth()->user()->recus()->withCount('depenses')->latest()->paginate()`

#### Scenario: Visit receipt list

- **WHEN** an authenticated user visits `/recus`
- **THEN** they SHALL see only their own receipts

#### Scenario: Unauthenticated access to list

- **WHEN** an unauthenticated user visits `/recus`
- **THEN** they SHALL be redirected to login

### Requirement: Create receipt form

The system SHALL provide a form to submit raw receipt text.

- The form SHALL have a textarea for `texte_brut`
- Submission SHALL go to `POST /recus`
- Validation SHALL use `StoreRecuRequest`
- On success, the receipt SHALL be created with `statut = en_attente` and the user SHALL be redirected to the receipt list with a success message

#### Scenario: Visit create form

- **WHEN** an authenticated user visits `/recus/create`
- **THEN** they SHALL see a form with a textarea

#### Scenario: Submit valid receipt

- **WHEN** a user submits valid receipt text
- **THEN** the receipt SHALL be created and the user SHALL be redirected to `/recus`

#### Scenario: Submit invalid receipt

- **WHEN** a user submits text shorter than 10 characters
- **THEN** validation errors SHALL be shown

### Requirement: View receipt detail

The system SHALL display a single receipt with its extracted expenses.

- The view SHALL show: receipt text, status, timestamps
- If `traite`: SHOW expense table with libelle, quantite, prix_unitaire, categorie
- If `echoue`: SHOW error message in red
- If `en_attente`: SHOW pending indicator
- Expenses SHALL be loaded via eager loading

#### Scenario: View processed receipt

- **WHEN** a user views a receipt with `statut = traite`
- **THEN** the expense table SHALL be displayed

#### Scenario: View failed receipt

- **WHEN** a user views a receipt with `statut = echoue`
- **THEN** the error message SHALL be displayed

#### Scenario: View pending receipt

- **WHEN** a user views a receipt with `statut = en_attente`
- **THEN** a pending indicator SHALL be shown

### Requirement: Delete receipt

The system SHALL allow users to delete their own receipts with cascade.

- The delete action SHALL be scoped to the authenticated user's receipts
- Deleting a receipt SHALL cascade delete its expenses
- On success, redirect to receipt list with success message

#### Scenario: Delete own receipt

- **WHEN** a user deletes their own receipt
- **THEN** the receipt and its expenses SHALL be deleted

#### Scenario: Delete another's receipt

- **WHEN** a user tries to delete another user's receipt
- **THEN** a 403 error SHALL be returned

### Requirement: Inline add expense from receipt detail

The system SHALL provide a button and inline form on the receipt detail view to add an expense without leaving the page.

- The receipt detail view SHALL show an "Add expense" button
- Clicking the button SHALL reveal an inline form with fields: libelle, quantite, prix_unitaire, categorie
- Form submission SHALL go to `POST /recus/{recu}/depenses`
- On success, the page SHALL show the new expense in the table
- Validation errors SHALL be displayed inline

#### Scenario: Add expense from receipt view
- **WHEN** a user clicks "Add expense" on a receipt detail page and submits valid data
- **THEN** the expense SHALL be created and displayed immediately in the expense table

### Requirement: Inline edit expense from receipt detail

The system SHALL provide edit actions on each expense row within the receipt detail view.

- Each expense row SHALL have an "Edit" button
- Clicking "Edit" SHALL replace the row with editable fields
- Submission SHALL go to `PUT /depenses/{depense}`
- The `recu_id` SHALL be preserved (not editable)
- On success, the row SHALL update with new values

#### Scenario: Edit expense from receipt view
- **WHEN** a user clicks "Edit" on an expense row and submits changes
- **THEN** the expense row SHALL display the updated values

### Requirement: Inline delete expense from receipt detail

The system SHALL provide a delete action on each expense row within the receipt detail view.

- Each expense row SHALL have a "Delete" button with confirmation
- Deleting SHALL use `DELETE /depenses/{depense}`
- On success, the row SHALL be removed from the table
- The parent receipt and other expenses SHALL remain unchanged

#### Scenario: Delete expense from receipt view
- **WHEN** a user confirms deletion of an expense from the receipt detail page
- **THEN** the expense row SHALL be removed and the receipt SHALL still exist
