# Gestion des Reçus — Design

## Overview

Four enhancements to receipt management: edit/update, search/filter, retry failed, total amount display.

---

## 1. Edit / Update Receipt

### Routes
Remove `->except(['edit', 'update'])` from the resource route, OR add explicit routes:
```php
Route::get('/recus/{recu}/edit', [RecuController::class, 'edit'])->name('recus.edit');
Route::put('/recus/{recu}', [RecuController::class, 'update'])->name('recus.update');
```

### Controller
- `edit(Recu $recu)`: scoped query `auth()->user()->recus()->findOrFail($recu->id)`, return view with `$recu`
- `update(UpdateRecuRequest $request, Recu $recu)`: scoped query, update fields, reset status + clear AI fields, dispatch Job, redirect

### Form Request
- `UpdateRecuRequest`: same rules as `StoreRecuRequest` (required, string, min:10, max:10000)

### View
- `recus/edit.blade.php`: same form as create, pre-filled with `$recu->texte_brut`, submit goes to `PUT /recus/{recu}`

### Buttons
- Index: "Modifier" link per row
- Show: "Modifier" link

---

## 2. Search & Filter

### Controller
Modify `index()`:
```php
$recus = auth()->user()->recus()
    ->withCount('depenses')
    ->when(request('search'), fn($q, $v) => $q->where('texte_brut', 'like', "%{$v}%"))
    ->when(request('status'), fn($q, $v) => $q->where('statut', $v))
    ->latest()
    ->paginate(15)
    ->appends(['search' => request('search'), 'status' => request('status')]);
```

### View
Add search input + status dropdown above the table, in a flex row. Form uses GET to same page. Use `request()` to pre-fill values.

---

## 3. Retry Failed Extraction

### Route
```php
Route::post('/recus/{recu}/retry', [RecuController::class, 'retry'])->name('recus.retry');
```

### Controller
```php
public function retry(Recu $recu): RedirectResponse
{
    $recu = auth()->user()->recus()->findOrFail($recu->id);

    if ($recu->statut !== StatutRecu::Echoue) {
        return back()->with('error', 'Ce reçu n\'est pas en échec.');
    }

    $recu->depenses()->delete();
    $recu->update([
        'statut' => StatutRecu::EnAttente,
        'payload_ia' => null,
        'message_erreur' => null,
    ]);

    ExtraireDepensesDuRecu::dispatch($recu->id);

    return to_route('recus.show', $recu)
        ->with('success', 'L\'extraction va être relancée.');
}
```

### View
Show `statut === 'echoue'`? Add form with POST to `recus.retry`.

---

## 4. Total Amount

### Controller
```php
$total = $recu->depenses->sum(fn($d) => $d->quantite * $d->prix_unitaire);
```
Pass `$total` to view.

### View
Add "Total: X.XX MAD" row at bottom of expenses table. If no expenses, show "—".

---

## Authorization Boundaries

| Route | Auth | Ownership |
|-------|------|-----------|
| `GET /recus/{recu}/edit` | `auth` middleware | `findOrFail` scoped |
| `PUT /recus/{recu}` | `auth` middleware | `findOrFail` scoped + `RecuPolicy::update()` |
| `POST /recus/{recu}/retry` | `auth` middleware | `findOrFail` scoped |

## Eager Loading Strategy

- Index: `withCount('depenses')` — no change
- Show: `with('depenses')` — no change (already loaded for total computation)
- Edit: no relations needed

## Test Strategy

- `RecuControllerTest` — add tests for edit, update, retry, filter
- Use `Queue::fake()` for update + retry (dispatch assertions)
- Use `RefreshDatabase` trait
- Test ownership isolation for all new endpoints
