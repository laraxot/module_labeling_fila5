---
type: story
tags: [security, labeling, qr, rate-limit]
epic: security-audit
story: B1-B2
slug: public-label-route-hardening
title: "Route pubblica /l/{code}: throttle e QR sempre su APP_URL"
status: review
phase: REVIEW
owner: Labeling
depends_on: []
related:
  - "./label-template-placeholders.story.md"
created: 2026-11-06
updated: 2026-11-06
---

# Route pubblica /l/{code}: throttle e QR sempre su APP_URL

## Story

Come **responsabile dell'impianto**
voglio **che i QR stampati puntino sempre al nostro dominio e che la route di risoluzione non si possa martellare**
così da **non stampare etichette che portano fuori e non permettere l'enumerazione veloce dei codici**.

## Perché / evidenze

- Audit **B2**: `BuildLabelUrlAction` usava `url('/l/...')`, che in una richiesta web prende l'Host della richiesta.
  Con `TRUSTED_HOSTS` vuoto un Host manipolato finiva nel QR stampato. Nel test la differenza è visibile:
  con la vecchia action il QR usciva con l'host della richiesta (`http://roosko.local`) invece di `APP_URL`.
- Audit **B1**: `GET /l/{code}` pubblica senza `throttle`: 302/404 permettono di enumerare i codici.

## Soluzione

- `BuildLabelUrlAction`: `rtrim(config('app.url'), '/').'/l/'.rawurlencode($code)`; `APP_URL` obbligatorio.
- Route `labeling.resolve` con `throttle:{labeling.resolve_rate_limit},1` (default 30 richieste/minuto per IP).

## Acceptance criteria

1. Con Host e origin della richiesta manipolati, il QR usa `APP_URL`.
2. La route ha il middleware `throttle`; dopo il limite risponde 429.

## Rischi residui

- La risposta distingue ancora codice esistente (302 verso il pannello) da inesistente (404): il throttle rallenta
  l'enumerazione ma non la elimina. Il redirect contiene l'id del record. Soluzione completa: route dietro `auth`
  con ritorno all'URL dopo il login (serve una route `login` comune ai pannelli, fuori da questo modulo).

## Dev agent record

- File: `app/Actions/BuildLabelUrlAction.php`, `routes/web.php`, `config/config.php`, `docs/README.md`.
- Test: `tests/Feature/LabelUrlTest.php` (host della richiesta ignorato, throttle presente, 429 dopo il limite).
