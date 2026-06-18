## ADDED Requirements

### Requirement: Expense belongs to user through receipt

The system SHALL provide a `belongsTo` relationship from Depense to User, accessible as `$depense->user`, traversing through the receipt.

- `Depense::user` SHALL be defined as `$this->receipt->user()`
- This SHALL be used for authorization checks without extra queries

#### Scenario: Access user from expense
- **WHEN** `$depense->user` is accessed
- **THEN** it SHALL return the User who owns the parent receipt
