## ADDED Requirements

### Requirement: Extraction lifecycle via Job

The system SHALL transition receipt status through the complete extraction lifecycle via the queued `ExtraireDepensesDuRecu` Job.

- `en_attente` → Job created and queued
- `traite` → Job completed successfully, expenses stored in database
- `echoue` → Job failed, error message stored in `message_erreur`
- The Job SHALL be the only mechanism for transitioning out of `en_attente`

#### Scenario: Lifecycle en_attente to traite
- **WHEN** the `ExtraireDepensesDuRecu` Job runs successfully
- **THEN** status transitions from `en_attente` to `traite`

#### Scenario: Lifecycle en_attente to echoue
- **WHEN** the `ExtraireDepensesDuRecu` Job fails
- **THEN** status transitions from `en_attente` to `echoue`
