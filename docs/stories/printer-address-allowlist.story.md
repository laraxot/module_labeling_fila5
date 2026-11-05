---
type: story
tags: [security, labeling, ssrf, network]
epic: security-audit
story: M2
slug: printer-address-allowlist
title: "Stampanti ZPL: solo indirizzi di LAN o consentiti (SSRF)"
status: review
phase: REVIEW
owner: Labeling
depends_on: []
related:
  - "../../../Weighing/docs/stories/scale-address-allowlist.story.md"
created: 2026-11-05
updated: 2026-11-05
---

# Stampanti ZPL: solo indirizzi di LAN o consentiti

## Story

Come **amministratore di sistema**
voglio **che il server apra connessioni di stampa solo verso stampanti della rete di stabilimento**
così da **non usare il gestionale come proxy per scandire la rete interna o i metadata del cloud**.

## Perché / evidenze

- Audit di sicurezza, rilievo **M2**: `ZplTcpPrinterDriver` apriva `tcp://{host}:{port}` dal record `Printer` senza
  vincoli; il form (`PrinterForm`) controllava solo `maxLength`. Con un template ZPL arbitrario si inviavano byte a
  servizi interni (es. Redis su 127.0.0.1:6379).
- Il messaggio d'errore riportava l'errore del socket (`Connection refused`, timeout…): un oracolo per la scansione delle porte.

## Soluzione

- `EnsurePrinterAddressAllowedAction::execute(?string $host, int $port): string`, usata dal form e dal driver:
  - host non vuoto, IP o hostname valido (niente `localhost`, niente forme abbreviate come `2130706433`, `0x7f000001`, `127.1`);
  - porta in `labeling.printer_ports` (default 1024–65535);
  - sempre vietati: metadata cloud, link-local, 0.0.0.0/8, multicast/riservati;
  - `labeling.allowed_printer_hosts` vuota → solo IP privati di LAN; valorizzata → solo voci elencate (IP, CIDR, hostname, `*.dominio`);
  - gli hostname si risolvono e il driver si collega all'IP controllato.
- `PrinterConnectionException::unreachable` non riporta più il motivo tecnico: va nel log (`Log::warning`).

## Acceptance criteria

1. Form stampante con host `127.0.0.1`, `169.254.169.254`, `localhost`, `8.8.8.8` o porta `22` → errore di validazione tradotto.
2. Driver con un host fuori lista → `PrinterAddressNotAllowedException` prima di `stream_socket_client`; stampa `failed`.
3. Con `allowed_printer_hosts` valorizzata passano solo le voci elencate; 169.254.169.254 mai.
4. Il messaggio di stampante irraggiungibile non contiene l'errore del socket.

## Dev agent record

- File: `app/Actions/EnsurePrinterAddressAllowedAction.php`, `app/Exceptions/PrinterAddressNotAllowedException.php`,
  `app/Exceptions/PrinterConnectionException.php`, `app/Drivers/ZplTcpPrinterDriver.php`,
  `app/Filament/Resources/PrinterResource/Schemas/PrinterForm.php`, `config/config.php`,
  `lang/{it,en}/exceptions.php`, `lang/{it,en}/printer_form.php`, `docs/README.md`.
- Test: `tests/Unit/EnsurePrinterAddressAllowedActionTest.php`, `tests/Feature/PrintLabelActionTest.php`
  (loopback/metadata/localhost/pubblico mai contattati; il test di stampante irraggiungibile mette 127.0.0.1 in lista).
- Logica degli indirizzi verificata anche con uno script PHP isolato (IPv4, IPv6, IPv4 mappati, forme abbreviate, CIDR).
- Rischio residuo: hostname solo IPv6 non si risolvono (`gethostbynamel`); usare l'IP.
