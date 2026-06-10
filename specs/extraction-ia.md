# Extraction IA

## Proposal
The AI call is slow — the page would freeze if run synchronously.
Dispatching a Job lets Si Brahim submit and immediately see
"En cours de traitement" while Groq works in the background.

## Spec
- Job: ExtraireDepensesDuRecu dispatched on store
- AI call via laravel/ai SDK (Groq provider)
- System prompt enforces strict JSON contract
- JSON contract: { articles: [{ libellé, quantité, prix_unitaire, catégorie }], total_estimé,       devise }
- On success: save Depenses, set statut=traite, store raw response in payload_brut
- On failure (malformed JSON, API error): set statut=echoue
- Queue driver: database

## Tasks
- [ ] Create ExtraireDepensesDuRecu job
- [ ] Configure laravel/ai with Groq in config/ai.php
- [ ] Write system prompt enforcing JSON contract
- [ ] Validate AI response before saving
- [ ] Save each article as a Depense row
- [ ] Handle failure → statut=echoue
- [ ] Run php artisan queue:work to process jobs
- [ ] Test full flow end to end