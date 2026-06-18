## ADDED Requirements

### Requirement: Category expense summary page

The system SHALL provide a dedicated page at `/depenses/summary` showing expense totals grouped by `CategorieDepense`.

- The page SHALL show a table with rows per category: category name (label), total count of expenses, total amount (`sum(quantite * prix_unitaire)`), and percentage of overall spending
- The page SHALL support an optional date range filter (`?date_from=&date_to=`)
- The page SHALL order categories by total amount descending
- A "Total général" row SHALL be displayed at the bottom
- The data SHALL be scoped to the authenticated user

#### Scenario: View category summary
- **WHEN** an authenticated user visits `/depenses/summary`
- **THEN** they SHALL see all categories with their totals

#### Scenario: Filtered category summary
- **WHEN** a user visits `/depenses/summary?date_from=2026-01-01&date_to=2026-06-30`
- **THEN** only expenses within that range are included in the totals

#### Scenario: Empty state
- **GIVEN** the user has no expenses
- **WHEN** they visit the summary page
- **THEN** they SHALL see "Aucune dépense pour cette période."

### Requirement: Tests

- Test that summary page renders with correct totals
- Test that filtered summary works
- Test that empty state is shown when no expenses
- Test data isolation
