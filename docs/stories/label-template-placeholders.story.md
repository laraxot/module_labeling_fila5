---
type: story
tags: [security, labeling, template, ssti]
epic: security-audit
story: C1
slug: label-template-placeholders
title: "Template etichette a segnaposto chiusi: niente Blade sui contenuti salvati a DB"
status: review
phase: REVIEW
owner: Labeling
depends_on: []
related:
  - "./print-without-printer-fails.story.md"
created: 2026-11-05
updated: 2026-11-05
---

# Template etichette a segnaposto chiusi

## Story

Come **responsabile dell'impianto**
voglio **che chi modifica un template di etichetta possa solo comporre testo e segnaposto**
così da **non dare a un ruolo applicativo l'esecuzione di codice sul server**.

## Perché / evidenze

- Audit di sicurezza, rilievo **C1** (critico): `RenderLabelAction` passava `$template->content`, campo libero
  del form `LabelTemplateForm`, a `Blade::render`. `@php system('id') @endphp` o `{{ file_get_contents(base_path('.env')) }}`
  venivano eseguiti alla prima stampa, anche di un operatore che non aveva scritto il template.
- In ZPL l'echo era senza escape, quindi l'output andava diretto alla stampante.
- In PDF Html2Pdf carica `<img src>` e `url()` da percorsi locali e URL: lettura di file e SSRF.

## Soluzione

- `RenderLabelAction` sostituisce solo segnaposto chiusi `{{ chiave }}` con `preg_replace_callback`, passata unica.
  Chiavi: `code`, `url`, `width_mm`, `height_mm`, `dpi`, `width_dots`, `height_dots`, `data.<chiave>`,
  `line_1..N` (valori di `getLabelData()` in ordine), `qr_svg` (solo PDF).
- Escape HTML (`htmlspecialchars`) per i PDF; per ZPL nessun escape HTML ma `^` e `~` dei dati diventano spazi (regola esistente, mantenuta).
- `ValidateLabelTemplateContentAction` (form + prima di ogni render) rifiuta `<?`, `{!! !!}`, direttive `@...`,
  segnaposto sconosciuti e, nei PDF, risorse esterne (`<img>`, `<link>`, `<script>`, `src=`, `href=`, `url(`).
- `LabelTemplateSeeder` riscritto senza `@foreach`: le prime quattro righe sono `{{ line_1 }}`..`{{ line_4 }}` a coordinate fisse.
- Helper text del campo `content` (it/en) con l'elenco delle chiavi.

## Acceptance criteria

1. Un template con `@php`, `{!! !!}`, `{{ espressione }}`, `<?php` non esegue nulla: la stampa fallisce con `LabelRenderException`.
2. Il form del template rifiuta lo stesso contenuto con un messaggio tradotto.
3. Un PDF con `<img src="/etc/passwd">` o `url(...)` viene rifiutato.
4. I valori nei PDF sono HTML-escaped; nei ZPL `^`/`~` sono neutralizzati e `&` resta `&`.
5. I template seedati sono validi e producono lo stesso layout di prima.

## Rischi residui

- I template personalizzati già salvati con sintassi Blade non stampano finché non vengono riscritti (fallimento visibile, non silenzioso).
- Consumer (Warehouse, Roosko) che in futuro seminano template propri devono usare la nuova sintassi.

## Dev agent record

- File: `app/Actions/RenderLabelAction.php`, `app/Actions/ValidateLabelTemplateContentAction.php`,
  `app/Exceptions/LabelRenderException.php`, `database/seeders/LabelTemplateSeeder.php`,
  `app/Filament/Resources/LabelTemplateResource/Schemas/LabelTemplateForm.php`, `lang/{it,en}/exceptions.php`,
  `lang/{it,en}/label_template_form.php`, `docs/README.md`.
- Test: `tests/Unit/RenderLabelActionTest.php` (segnaposto ZPL/PDF, escape, nessuna ri-espansione, dataset di
  payload Blade/PHP non eseguiti, PDF con risorse esterne rifiutato, template seedati validi).
- Verifica: `php -l` sui file toccati, `check_refs.php` senza riferimenti non risolti; Pest non eseguibile
  nell'ambiente dell'audit (nessun vendor).
