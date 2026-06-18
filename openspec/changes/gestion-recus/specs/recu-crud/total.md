# Recu — Total Amount

## Purpose

Display the computed total expense amount on the receipt detail page.

## Requirements

### Requirement: Compute total on show page

The system SHALL compute and display the sum of all expenses for a receipt.

- The controller's `show()` method SHALL add a `$total` variable computed from `$recu->depenses->sum(fn($d) => $d->quantite * $d->prix_unitaire)`
- The show view SHALL display a "Total" row at the bottom of the expenses table, right-aligned
- The total SHALL be formatted with 2 decimal places and the MAD currency symbol
- If there are no expenses (receipt is `en_attente` or `echoue`), SHALL display "—" or "En attente de traitement"

#### Scenario: Display total for processed receipt
- GIVEN a receipt with `statut = traite` and multiple expenses
- WHEN viewing the receipt detail
- THEN the total amount is displayed (e.g., "Total: 123.45 MAD")

#### Scenario: No expenses yet
- GIVEN a receipt with `statut = en_attente` or `echoue`
- WHEN viewing the receipt detail
- THEN "—" is shown for the total

### Requirement: Tests

- Test that show page displays correct total for receipt with expenses
- Test that show page displays placeholder when receipt has no expenses
