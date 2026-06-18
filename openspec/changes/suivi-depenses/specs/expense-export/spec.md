## ADDED Requirements

### Requirement: Export expenses to CSV

The system SHALL allow exporting the current filtered expense list as a CSV file.

- Route: `GET /depenses/export` → `DepensesController::export()`
- The export SHALL respect the same query parameters as the index page (`categorie`, `date_from`, `date_to`)
- The response SHALL be a CSV file download with headers: `Libellé, Quantité, Prix unitaire, Total, Catégorie, Date du reçu`
- Each row SHALL contain one expense with the category label (not the enum value)
- The file name SHALL be `depenses_export_YYYY-MM-DD.csv` (current date)
- The data SHALL be scoped to the authenticated user

#### Scenario: Export all expenses
- **WHEN** a user clicks "Exporter en CSV" on the expense index page
- **THEN** a CSV file with all their expenses is downloaded

#### Scenario: Export filtered expenses
- **WHEN** a user has active category or date filters and clicks export
- **THEN** the CSV contains only the filtered subset of expenses

#### Scenario: Empty export
- **GIVEN** the user has no expenses matching the current filters
- **WHEN** they click export
- **THEN** a CSV with only the header row is downloaded

### Requirement: Export button in expense index

The expense index view SHALL display an "Exporter en CSV" link visible when the user has expenses.

### Requirement: Tests

- Test that export returns CSV with correct headers
- Test that export respects category filter
- Test that export respects date range filter
- Test that unauthorized users cannot export
