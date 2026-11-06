---
type: story
tags: [security, labeling, authorization, permissions, rate-limit]
epic: security-audit
story: A2-A3-M4-B5
slug: labeling-authorization
title: "Labeling: policy che negano le abilità non dichiarate, azioni autorizzate, permessi seminati, rate limit sulla stampa"
status: review
phase: REVIEW
owner: Labeling
depends_on: []
related:
  - "../../../Weighing/docs/stories/weighing-authorization.story.md"
created: 2026-11-06
updated: 2026-11-06
---

# Labeling: autorizzazione completa

## Story

Come **responsabile dell'impianto**
voglio **dare agli operatori solo i permessi che servono (stampare, non modificare template; leggere, non cancellare)**
così da **non dover dare a tutti il ruolo super-admin per usare il modulo**.

## Perché / evidenze

- **A2**: Filament considera consentita un'abilità che la policy non dichiara: con `viewAny` si eliminavano in blocco
  template e stampanti (`HasXotTable::getTableBulkActions()`).
- **A3**: stampa, ristampa, download PDF e revoca identificatore controllavano solo lo stato del record.
- **M4**: i permessi delle policy non venivano mai creati: `hasPermissionTo()` lanciava `PermissionDoesNotExist`.
- **B5**: nessun rate limit sulla stampa (connessioni TCP e PDF ripetibili senza limite).

## Soluzione

- `DeniesUndeclaredFilamentAbilities` in `IdentifierPolicy`, `LabelPrintPolicy`, `LabelTemplatePolicy`, `PrinterPolicy`.
- Stampa: `authorize()` con `Gate::allows('create', LabelPrint::class)` + controllo ripetuto prima di chiamare `PrintLabelAction`;
  ristampa `authorize('create')`; download `authorize('view')`; revoca `authorize('update')`.
- `LabelingPermissionSeeder`: permessi delle policy + ruolo `labeling::admin`, idempotente, chiamato dal DatabaseSeeder.
- `rateLimit(labeling.print_rate_limit)` su stampa e ristampa.

## Acceptance criteria

1. Ogni policy nega `deleteAny`, `forceDeleteAny`, `reorder` a chi non è super-admin.
2. Senza abilità i bottoni stampa/ristampa/download/revoca non sono autorizzati; con l'abilità sì.
3. Il seeder crea esattamente i permessi usati dalle policy (test che li estrae dai file).
4. Stampa e ristampa hanno un rate limit configurabile.

## Dev agent record

- File: `app/Models/Policies/*.php`, `app/Filament/Actions/Concerns/ConfiguresPrintLabelAction.php`,
  `app/Filament/Resources/LabelPrintResource/Tables/LabelPrintsTable.php`,
  `app/Filament/Resources/IdentifierResource/Tables/IdentifiersTable.php`, `database/seeders/LabelingPermissionSeeder.php`,
  `database/seeders/LabelingDatabaseSeeder.php`, `config/config.php`, `docs/README.md`.
- Test: `tests/Feature/LabelingAuthorizationTest.php` (Gate::before del test con utente Authorizable fittizio, come in Processing).
- Rischio residuo: il ruolo `labeling::admin` ha tutti i permessi; ruoli operativi più stretti (es. solo stampa) vanno definiti dall'impianto.
