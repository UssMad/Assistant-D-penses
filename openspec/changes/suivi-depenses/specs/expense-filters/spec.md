## ADDED Requirements

### Requirement: Filter expenses by date range

The system SHALL allow filtering the expense list by a date range in addition to the existing category filter.

- The expense index view SHALL include two date inputs: `date_from` and `date_to`
- The inputs SHALL be HTML date inputs (type="date")
- The controller's `index()` method SHALL apply `->when(request('date_from'), fn($q, $v) => $q->whereDate('created_at', '>=', $v))` and `->when(request('date_to'), fn($q, $v) => $q->whereDate('created_at', '<=', $v))`
- The date filter SHALL compose with the existing category filter (`?categorie=&date_from=&date_to=`)
- Pagination links SHALL preserve all filter parameters via `->appends()`
- The filter SHALL scope to the authenticated user's expenses

#### Scenario: Filter by date range
- **WHEN** a user selects a date range on the expense index page
- **THEN** only expenses within that range are shown

#### Scenario: Combined category + date filter
- **WHEN** a user selects a category AND a date range
- **THEN** only expenses matching both filters are shown

#### Scenario: Clear filters
- **WHEN** a user clicks "Effacer" when filters are active
- **THEN** all filters are removed and all expenses are shown

### Requirement: Tests

- Test that `?date_from=` and `?date_to=` filter correctly
- Test that combined category + date filters work
- Test that invalid dates are ignored
- Test data isolation
