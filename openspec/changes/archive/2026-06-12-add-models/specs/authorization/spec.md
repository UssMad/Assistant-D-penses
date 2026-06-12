## ADDED Requirements

### Requirement: User data isolation via policy

The system SHALL provide a `RecuPolicy` that ensures users can only access their own receipts and expenses.

- The policy SHALL check `$user->id === $recu->user_id` for `view`, `create`, `update`, `delete` actions
- Controllers SHALL scope receipt queries via `auth()->user()->recus()` for listing
- Controllers SHALL check policy for individual receipt access
- Route model binding alone SHALL NOT be trusted without ownership verification

#### Scenario: User views own receipt

- **WHEN** a user attempts to view a receipt they own
- **THEN** access SHALL be granted

#### Scenario: User views another's receipt

- **WHEN** a user attempts to view a receipt owned by another user
- **THEN** access SHALL be denied with 403

#### Scenario: User lists own receipts

- **WHEN** a user fetches their receipt list via `auth()->user()->recus()`
- **THEN** they SHALL only see their own receipts

### Requirement: User model relationship

The system SHALL provide a `hasMany` relationship from User to Recu.

- `User::recus()` SHALL return all receipts owned by the user
- `User::depenses()` SHALL return all expenses through receipts via `hasManyThrough`

#### Scenario: Access user receipts

- **WHEN** `auth()->user()->recus()->get()` is called
- **THEN** it SHALL return only receipts belonging to that user

#### Scenario: Access user expenses through receipts

- **WHEN** `auth()->user()->recus()->with('depenses')->get()` is called
- **THEN** it SHALL return receipts with their nested expenses, all scoped to the user
