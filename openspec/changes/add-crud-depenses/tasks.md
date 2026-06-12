## 1. Controller & Routes

- [ ] 1.1 Create `DepenseController` with index, create, store, edit, update, destroy methods
- [ ] 1.2 Register routes: `GET /depenses`, `POST /recus/{recu}/depenses`, `GET|PUT /depenses/{depense}`, `DELETE /depenses/{depense}`
- [ ] 1.3 Create `DepensePolicy` with view, create, update, delete gates
- [ ] 1.4 Register `DepensePolicy` in `AuthServiceProvider`

## 2. Form Requests & Validation

- [ ] 2.1 Create `StoreDepenseRequest` with rules: recu_id (exists, belongs to user), libelle (required, string, max:255), quantite (required, integer, min:1), prix_unitaire (required, numeric, min:0), categorie (required, in enum values)
- [ ] 2.2 Create `UpdateDepenseRequest` with same rules minus recu_id (not changeable)

## 3. Views

- [ ] 3.1 Create `depenses/index.blade.php` with paginated list, category filter dropdown, action buttons
- [ ] 3.2 Create `depenses/create.blade.php` with form (standalone, includes recu_id selector of user's receipts)
- [ ] 3.3 Create `depenses/edit.blade.php` with form pre-filled from existing expense
- [ ] 3.4 Create inline expense form partial for receipt detail view
- [ ] 3.5 Update `recus/show.blade.php` with "Add expense" button, inline edit/delete on each row

## 4. Tests

- [ ] 4.1 Test unauthenticated user is redirected to login for all expense routes
- [ ] 4.2 Test listing expenses with category filter
- [ ] 4.3 Test creating expense with valid/invalid data and receipt ownership validation
- [ ] 4.4 Test editing own expense and 403 for another user's expense
- [ ] 4.5 Test deleting own expense individually (receipt and other expenses preserved)
- [ ] 4.6 Test 403 for accessing another user's expense
