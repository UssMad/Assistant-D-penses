## Why

Currently, receipts are saved with `en_attente` status but no AI extraction ever runs — expenses are never created from receipt text. The core value proposition of the app (Darija receipt → structured expenses) is missing.

## What Changes

- Create `ExtraireDepensesDuRecu` Job that calls Groq via laravel/ai SDK to extract structured expenses from raw receipt text
- Dispatch the Job after receipt creation in `RecuController::store()`
- Update receipt status to `traite` on success (with expenses stored) or `echoue` on failure (with error message)
- Switch default AI provider from `openai` to `groq` in `config/ai.php`
- Add polling or manual refresh pattern on receipt detail view for pending receipts

## Capabilities

### New Capabilities
- `extraction-depenses`: Asynchronous AI extraction of structured expense line items from raw receipt text (Darija), with status tracking and error handling

### Modified Capabilities
- `recu-crud`: Receipt creation must now dispatch the extraction Job; receipt detail view should auto-refresh or allow manual refresh for pending status
- `receipts`: Update to reflect the complete lifecycle (en_attente → Job dispatched → traite/echoue)

## Impact

- New Job: `app/Jobs/ExtraireDepensesDuRecu.php`
- Modified: `app/Http/Controllers/RecuController.php` — dispatch Job after store
- Modified: `config/ai.php` — set `default` to `groq`
- Modified: `routes/web.php` — add refresh route for pending receipts if needed
- Modified: `resources/views/recus/show.blade.php` — add manual refresh button for pending status
- Tests for Job dispatch, successful extraction, failed extraction
- .env.example update with GROQ_API_KEY

## User Stories

- As a shop owner, I paste receipt text and see "En attente" immediately, so I know my request was received.
- As a shop owner, I return later and see my expenses extracted and categorized, without having waited.
- As a shop owner, if extraction fails, I see a clear error message so I know what went wrong.

## Non-goals

- Real-time/push notifications when extraction completes
- Retry mechanism for failed extractions (manual re-submit only)
- Multiple AI provider fallback (Groq only)
- Image/PDF receipt scanning (text paste only)
