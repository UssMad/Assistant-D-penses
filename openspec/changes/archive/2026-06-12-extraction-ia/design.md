## Context

Receipts are created with `en_attente` status but no extraction pipeline exists. The `config/ai.php` has a Groq provider configured (with API key in `.env`) but the default provider is `openai`. The AI extraction contract (structured output with articles/total/devise) is defined in the project spec but no code implements it. The queue driver is already set to `database` in `.env`.

## Goals / Non-Goals

**Goals:**
- Dispatch a queued Job after receipt creation to call Groq via laravel/ai SDK
- Store extracted expenses as Depense records linked to the receipt
- Handle success (traite + expenses + payload_ia) and failure (echoue + message_erreur)
- Allow users to refresh the receipt detail page to see updated status
- Switch AI default provider to Groq

**Non-Goals:**
- WebSocket/polling auto-refresh
- Retry logic for failed jobs
- Multi-provider fallback
- Image/PDF scanning

## Decisions

### Decision: Job reads receipt by ID from database

The Job receives `$recuId` and loads the Recu from DB. This avoids serializing the entire model and ensures fresh data.

**Rationale**: Laravel Jobs should serialize minimal data. Loading from DB also avoids stale model issues if the queue worker delays.

### Decision: Single Job class handles both success and failure

`ExtraireDepensesDuRecu` handles the AI call, parses the structured output, creates Depense records on success, or updates receipt to `echoue` on failure — all in the `handle()` method using try/catch.

**Rationale**: Keeps the Job self-contained. The alternative (separate success/failure jobs) adds complexity without benefit for this simple pipeline.

### Decision: Direct `Ai::driver('groq')->chat()` call in Job

The Job uses `Ai::driver('groq')->createChat()` with the system prompt and receipt text, requesting structured output matching the defined schema.

**Rationale**: Aligns with the AGENTS.md constraint ("AI calls go through laravel/ai SDK only"). No raw HTTP calls.

### Decision: Default provider changed to Groq

Update `config/ai.php` so `'default' => 'groq'` to avoid specifying the driver in every call.

**Rationale**: Groq is the only AI provider used by this app. Setting it as default simplifies all SDK calls.

### Decision: Manual refresh button instead of auto-polling

A "Refresh" button is added to the receipt detail view when status is `en_attente`. No JavaScript auto-refresh or WebSocket.

**Rationale**: Simple, no additional dependencies. The user understands they need to wait for queue processing.

## Risks / Trade-offs

- [Risk] Groq API could be slow or unavailable → Mitigation: Job runs async, failure transitions to `echoue` with error message
- [Risk] AI may return invalid or malformed structured output → Mitigation: try/catch around the parse, invalid responses treated as failure
- [Risk] Queue worker may not be running → Mitigation: Document in setup instructions that `php artisan queue:work` must run
