## 1. Configuration

- [x] 1.1 Update `config/ai.php` default provider to `groq`

## 2. Job Implementation

- [x] 2.1 Create `app/Jobs/ExtraireDepensesDuRecu.php` with receipt ID constructor and handle method
- [x] 2.2 Implement AI call using `Ai::driver('groq')->chat()` with structured output for Darija receipt text
- [x] 2.3 Implement success path: parse structured response, create Depense records, update receipt to `traite`, store `payload_ia`
- [x] 2.4 Implement failure path: catch exceptions, update receipt to `echoue`, store readable `message_erreur`

## 3. Controller & View Updates

- [x] 3.1 Update `RecuController::store()` to dispatch `ExtraireDepensesDuRecu` Job after receipt creation
- [x] 3.2 Update `recus/show.blade.php` to show "Refresh" button when status is `en_attente`

## 4. Tests

- [x] 4.1 Test Job is dispatched after receipt creation (Queue::fake)
- [x] 4.2 Test successful extraction creates expenses and updates receipt to `traite` (Ai::fake)
- [x] 4.3 Test failed extraction updates receipt to `echoue` with error message (Ai::fake with exception)
- [x] 4.4 Test invalid AI response (missing fields) is handled as failure
