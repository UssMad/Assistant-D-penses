## 1. Routes

- [x] 1.1 Add resource routes for `recus` and index/show routes for `depenses` in `routes/web.php`
- [x] 1.2 Write route existence tests

## 2. RecuController

- [x] 2.1 Implement `index` — list user's receipts with status badges, expense counts, pagination
- [x] 2.2 Implement `create` — show receipt form view
- [x] 2.3 Implement `store` — validate with `StoreRecuRequest`, create receipt, redirect
- [x] 2.4 Implement `show` — display receipt with eager-loaded expenses, status-aware UI
- [x] 2.5 Implement `destroy` — delete receipt with cascade, redirect
- [x] 2.6 Write controller tests for all actions

## 3. DepensesController

- [x] 3.1 Implement `index` — list user's expenses through receipts with eager loading
- [x] 3.2 Implement `show` — display single expense with receipt context
- [x] 3.3 Write controller tests

## 4. Blade Views

- [x] 4.1 Create `recus/index.blade.php` — table with status badges (color-coded), action buttons, pagination
- [x] 4.2 Create `recus/create.blade.php` — form with texte_brut textarea
- [x] 4.3 Create `recus/show.blade.php` — receipt detail + expense table, status-aware sections
- [x] 4.4 Create `depenses/index.blade.php` — expense table with category badges
- [x] 4.5 Create `depenses/show.blade.php` — single expense detail
- [x] 4.6 Add navigation links to layout sidebar/navbar for receipts and expenses
