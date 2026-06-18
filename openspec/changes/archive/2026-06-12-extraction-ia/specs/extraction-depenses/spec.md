## ADDED Requirements

### Requirement: Asynchronous AI extraction via Job

The system SHALL dispatch `ExtraireDepensesDuRecu` Job after a receipt is created to extract structured expenses from the raw receipt text using the Groq AI provider via laravel/ai SDK.

- The Job SHALL be dispatched on the `default` queue after the receipt is saved
- The Job SHALL receive the `Recu` ID and load the receipt within the Job
- The Job SHALL use `laravel/ai` structured output with a defined schema matching the extraction contract
- On success: receipt status SHALL be `traite`, extracted expenses SHALL be stored as `Depense` records, and raw AI payload SHALL be stored in `payload_ia`
- On failure: receipt status SHALL be `echoue` and `message_erreur` SHALL store a readable French error message

#### Scenario: Job dispatches on receipt creation
- **WHEN** a receipt is created with `texte_brut`
- **THEN** `ExtraireDepensesDuRecu` Job SHALL be queued

#### Scenario: Successful extraction creates expenses
- **WHEN** the Job processes a valid receipt text and Groq returns a valid structured response
- **THEN** the receipt status SHALL become `traite`, `Depense` records SHALL be created for each article, and `payload_ia` SHALL store the raw response

#### Scenario: Failed extraction stores error
- **WHEN** the Job processes a receipt and Groq returns an error or invalid response
- **THEN** the receipt status SHALL become `echoue` and `message_erreur` SHALL contain a readable error message

### Requirement: AI extraction prompt and schema

The system SHALL use a French-language system prompt targeting Darija receipt text to extract structured expense data.

- The prompt SHALL instruct the AI to extract articles from an informal supplier receipt written in Darija
- The structured output schema SHALL match: `articles: [{ libelle: string, quantite: integer, prix_unitaire: number, categorie: string }], total_estime: number, devise: string`
- The `categorie` field SHALL map to `CategorieDepense` enum values (`alimentaire`, `boissons`, `hygiene`, `entretien`, `autre`)
- The prompt SHALL instruct the AI to return `autre` when category is uncertain

#### Scenario: Valid structured output
- **WHEN** the AI returns `{"articles": [{"libelle": "Pain", "quantite": 2, "prix_unitaire": 1.5, "categorie": "alimentaire"}], "total_estime": 3.0, "devise": "MAD"}`
- **THEN** one `Depense` record SHALL be created with matching fields
