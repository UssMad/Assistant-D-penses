## ADDED Requirements

### Requirement: Expense dashboard with summary cards

The system SHALL display an expense dashboard when the authenticated user visits `/dashboard`.

- The dashboard SHALL replace the generic "You're logged in!" view
- The dashboard SHALL show four summary cards in a grid:
  1. **Total du mois** — sum of all expenses this month (current month, `quantite * prix_unitaire`), formatted with 2 decimals and MAD
  2. **Dépenses par catégorie** — a breakdown showing each `CategorieDepense` with its total amount for the current month, sorted by amount descending
  3. **Dernières dépenses** — the 5 most recent expenses (libelle, amount, date)
  4. **Reçus en attente** — count of receipts with `statut = en_attente`
- The data SHALL be scoped to the authenticated user only
- The dashboard SHALL use eager loading and aggregated queries (no N+1)

#### Scenario: Authenticated user views dashboard
- **WHEN** an authenticated user visits `/dashboard`
- **THEN** they SHALL see the four summary cards with their data

#### Scenario: Unauthenticated access to dashboard
- **WHEN** an unauthenticated user visits `/dashboard`
- **THEN** they SHALL be redirected to login

#### Scenario: Dashboard shows correct monthly total
- **GIVEN** a user has expenses in the current month and previous months
- **WHEN** they view the dashboard
- **THEN** only the current month's total is shown in the "Total du mois" card

### Requirement: Tests

- Test that dashboard renders for authenticated user
- Test that dashboard is inaccessible to guests
- Test that monthly total is correct
- Test that pending receipts count is correct
- Test that data isolation is enforced
