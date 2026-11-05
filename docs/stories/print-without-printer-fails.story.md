---
type: story
tags: [labeling, printing, ux]
epic: functional-audit
story: print-without-printer
slug: print-without-printer-fails
title: "Stampa senza stampante: fallimento visibile invece di silenzio"
status: review
phase: REVIEW
owner: Labeling
depends_on:
  - "./label-template-placeholders.story.md"
related: []
created: 2026-11-05
updated: 2026-11-05
---

# Stampa senza stampante: fallimento visibile

## Story

Come **operatore di piazzale**
voglio **sapere subito quando un'etichetta non è stata stampata e perché**
così da **non attaccare un collo senza etichetta convinto che sia in stampa**.

## Perché / evidenze

- Audit funzionale, sezione Labeling: "Template ZPL di default e nessuna stampante: stato `rendered`, niente stampato
  e nessun file scaricabile. L'operatore riceve comunque 'etichetta in stampa'".
- Flusso di accettazione (`AcceptDeliveryAction` → `QueueUnitLabelAction` → `PrintLabelAction`, in coda): senza
  stampante nessun errore, nessun job fallito.
- `PrintLabelAction` restituiva `RENDERED` quando non trovava un driver (ZPL senza stampante).

## Soluzione

- `PrintLabelAction`: se il template è ZPL e non c'è stampante (passata o di default attiva) lancia
  `PrinterNotConfiguredException`, dopo aver salvato la `LabelPrint` in `failed` con il motivo in `error`.
- Anche un template non valido ora lascia una `LabelPrint` `failed` (snapshot vuoto, motivo in `error`): le stampe in
  coda che falliscono restano nello storico.
- Le azioni Filament (`PrintLabelTableAction`, `PrintLabelHeaderAction`, ristampa) già intercettano `LabelingException`
  e mostrano una notifica di errore con il messaggio tradotto.
- `rendered` ora significa solo "PDF generato, da scaricare" (lang e PHPDoc aggiornati).

## Acceptance criteria

1. Template ZPL, nessuna stampante: eccezione di dominio, `LabelPrint` `failed` con `error` valorizzato e `printed_at` null.
2. Template non valido: `LabelPrint` `failed` con il motivo.
3. Template PDF senza stampante: il PDF si produce come prima (`rendered` + `output_path`).
4. Nella UI la stampa fallita produce una notifica di errore, non "etichetta in stampa".

## Note per i consumer

- `QueueUnitLabelAction` (Roosko) gira in coda: il job ora fallisce quando manca la stampante. Se la coda è `sync`
  dentro una richiesta, l'eccezione `LabelingException` (RuntimeException) va intercettata dal chiamante.

## Dev agent record

- File: `app/Actions/PrintLabelAction.php`, `app/Exceptions/PrinterNotConfiguredException.php`,
  `app/Enums/LabelPrintStatusEnum.php`, `lang/{it,en}/exceptions.php`, `lang/{it,en}/label_print_status_enum.php`,
  `config/config.php`, `docs/README.md`.
- Test: `tests/Feature/PrintLabelActionTest.php` (ZPL senza stampante → failed con motivo; template non valido → failed).
