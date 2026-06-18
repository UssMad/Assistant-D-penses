## MODIFIED Requirements

### Requirement: User data isolation via policy

The system SHALL provide a `RecuPolicy` that ensures users can only access their own receipts and expenses. Controllers SHALL enforce this policy via `$this->authorize()` in each method.

- The policy SHALL check `$user->id === $recu->user_id` for `view`, `create`, `update`, `delete` actions
- Controllers SHALL scope receipt queries via `auth()->user()->recus()` for listing
- Controllers SHALL call `$this->authorize()` for individual receipt actions (show, update, delete)
- Controllers SHALL call `$this->authorize('create', Recu::class)` for the create action
- Route model binding alone SHALL NOT be trusted without ownership verification

#### Scenario: Authorized receipt show

- **WHEN** a controller calls `$this->authorize('view', $recu)`
- **THEN** access SHALL be granted if the user owns the receipt

#### Scenario: Unauthorized receipt show

- **WHEN** a controller calls `$this->authorize('view', $recu)`
- **THEN** access SHALL be denied with 403 if the user does not own the receipt

#### Scenario: Create action authorization

- **WHEN** a controller calls `$this->authorize('create', Recu::class)`
- **THEN** any authenticated user SHALL be allowed

### Requirement: User model relationship

*(unchanged from main spec)*

- `User::recus()` SHALL return all receipts owned by the user
- `User::depenses()` SHALL return all expenses through receipts via `hasManyThrough`
