# Recus CRUD

## Proposal
Si Brahim needs to submit, view, and delete receipts.
Each receipt tracks its AI processing status.

## Spec
- Model: Recu (user_id FK, texte_source, statut enum, payload_brut json)
- Enum cast: StatutRecu (en_attente, traite, echoue)
- Array cast: payload_brut
- Relation: Recu hasMany Depense
- Validation: StoreRecuRequest (texte_source required, min:20, max:5000)
- Index: withCount('depenses'), zero N+1
- Delete: cascade on depenses

## Tasks
- [ ] Create Recu and Depense migrations
- [ ] Create StatutRecu and CategorieDepense enums
- [ ] Create Recu and Depense models with casts and relations
- [ ] Create StoreRecuRequest
- [ ] Create RecuController (index, create, store, show, destroy)
- [ ] Create blade views (index, create, show)
- [ ] Verify zero N+1 with Debugbar