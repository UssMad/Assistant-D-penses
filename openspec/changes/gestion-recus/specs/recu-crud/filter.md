# Recu — Filter & Search

## Purpose

Allow users to filter the receipt list by status and search by text content.

## Requirements

### Requirement: Filter by status

The system SHALL allow filtering the receipt list by `statut`.

- The index view SHALL include a dropdown select with options: "Tous", "En attente", "Traité", "Échoué"
- The dropdown SHALL map to enum values (`en_attente`, `traite`, `echoue`)
- Selection SHALL send `?status=` query parameter
- The controller's `index()` method SHALL apply `->when(request('status'), fn($q, $v) => $q->where('statut', $v))`
- The selected value SHALL persist in the dropdown after page reload
- Pagination links SHALL preserve the `status` query parameter (`->appends(['status' => request('status')])`)
- The filter SHALL scope to the authenticated user's receipts

### Requirement: Search by text

The system SHALL allow searching receipts by `texte_brut` content.

- The index view SHALL include a text input with placeholder "Rechercher dans le texte..."
- The search input SHALL send `?search=` query parameter
- The controller's `index()` method SHALL apply `->when(request('search'), fn($q, $v) => $q->where('texte_brut', 'like', "%{$v}%"))`
- Search and filter SHALL compose together (e.g., `?search=tomates&status=en_attente`)
- Pagination links SHALL preserve both `search` and `status` parameters

### Requirement: Empty results state

When filters return no results, SHALL display "Aucun reçu ne correspond à votre recherche."

#### Scenario: Filter receipts by status
- WHEN a user selects "En attente" from the filter dropdown
- THEN only receipts with `statut = en_attente` are shown

#### Scenario: Search receipts by text
- WHEN a user types "tomates" in the search field
- THEN only receipts whose `texte_brut` contains "tomates" are shown

#### Scenario: Combined filter + search
- WHEN a user selects "Échoué" AND types "tomates"
- THEN only failed receipts containing "tomates" are shown

### Requirement: Tests

- Test that `?status=en_attente` filters correctly
- Test that `?search=keyword` filters correctly
- Test that combined params work together
- Test that invalid status values are ignored (returns all)
