# Gestion des Reçus — Proposal

## Problem
Current receipt management is minimal: create, view, delete. Users cannot edit a receipt's raw text after creation, search/filter the receipt list, retry failed extractions, or see total amounts.

## User Stories
- **Edit receipt**: "I typed the wrong text — let me fix it without deleting and starting over."
- **Filter receipts**: "Show me only failed receipts so I can retry them."
- **Search receipts**: "Find the receipt that had 'tomates' in the text."
- **Retry failed**: "The AI call failed on this receipt — let me retry it."
- **See total**: "How much is this receipt worth?"

## Scope
### Goals
1. **Edit/Update receipt** — Add `edit` + `update` to `RecuController` (routes, views, form request). Policy + tests already exist.
2. **Search & filter** — Filter by status, search by `texte_brut` on the index page.
3. **Retry failed extraction** — "Réessayer" button on `echoue` receipts re-dispatches the Job.
4. **Receipt total** — Compute and display total expense amount on receipt detail.

### Non-goals
- No image upload (future change)
- No export (future change)
- No dashboard integration (future change)
- No sortable columns (future change)

## Specs needed
- `recu-crud/update.md` — edit receipt spec
- `recu-crud/filter.md` — search/filter spec  
- `recu-crud/retry.md` — retry failed receipt spec
- `recu-crud/total.md` — total amount display spec

## Open Questions
None.
