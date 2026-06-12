## ADDED Requirements

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
