# Modulo Labeling

Etichettatura generica per qualunque modello Eloquent: identificatori fisici (QR, Code 128, RFID UHF, NFC), template di etichetta ZPL o PDF, stampanti e storico stampe. Il modulo non sa nulla di rifiuti né di magazzino: i consumer (Warehouse, Roosko) implementano il contratto e usano le azioni.

Dipende solo da Xot (e User per le policy). Nuova dipendenza composer: `chillerlan/php-qrcode`. I PDF usano `spipu/html2pdf`, già richiesto da Xot.

## Contratto per i consumer

```php
use Modules\Labeling\Contracts\LabelableContract;
use Modules\Labeling\Models\Traits\HasIdentifiers;

class StockUnit extends BaseModel implements LabelableContract
{
    use HasIdentifiers; // identifiers(), labelPrints(), activeIdentifier(IdentifierTypeEnum $type)

    public function getLabelCode(): string { return $this->code; }

    /** @return array<string, string|int|float|null> */
    public function getLabelData(): array { return ['container' => $this->container_type->getLabel()]; }
}
```

Il consumer registra la classe per il campo "tipo di elemento" dei template, dal proprio ServiceProvider:

```php
config()->push('labeling.labelables', StockUnit::class);
```

Azioni Filament riusabili, entrambe senza argomenti:

```php
'print_label' => PrintLabelTableAction::make(),   // in getTableActions() della *Table
'print_label' => PrintLabelHeaderAction::make(),  // in getHeaderActions() di View/Edit
```

Le due azioni prendono etichetta, tooltip e testi dal lang di Labeling (`print_label_table.php`, `print_label_header.php`), non da quello della Resource che le usa. Se producono un PDF lo restituiscono come download.

## Modelli (connessione `labeling`)

| Modello | Tabella | Note |
|---|---|---|
| `LabelTemplate` | `label_templates` | `content` testo con segnaposto `{{ chiave }}` (mai Blade); default per classe (`labelable_type` + `is_default`) o generico (`labelable_type` null) |
| `Printer` | `printers` | `driver` = `PrinterDriverEnum` (`zpl_tcp`, `pdf_download`) |
| `Identifier` | `identifiers` | morph `identifiable`; `active_value` = `"{type}:{value}"` solo se attivo, con indice unique |
| `LabelPrint` | `label_prints` | morph `labelable`; snapshot `rendered`, `status`, `error`, `output_path` (PDF) |

L'unicità "valore attivo per tipo" la garantisce il database tramite l'indice unique su `active_value`: alla revoca la colonna torna `null`, e più `NULL` sono ammessi. L'indice `(type, value, is_active)` della SPEC resta per le ricerche.

## Azioni

| Azione | Firma |
|---|---|
| `AssignIdentifierAction` | `execute(Model&LabelableContract $model, IdentifierTypeEnum $type, ?string $value = null): Identifier` |
| `RevokeIdentifierAction` | `execute(Identifier $identifier): Identifier` |
| `ResolveIdentifiableAction` | `execute(string $scanned): ?Model` |
| `NormalizeScannedCodeAction` | `execute(string $scanned): NormalizedScanData` (pura) |
| `RenderLabelAction` | `execute(Model&LabelableContract $model, LabelTemplate $template): string` |
| `ValidateLabelTemplateContentAction` | `execute(string $content, LabelFormatEnum $format): ?string` (messaggio d'errore tradotto o null) |
| `PrintLabelAction` (queueable) | `execute(Model&LabelableContract $model, ?LabelTemplate $template = null, ?Printer $printer = null, int $copies = 1): LabelPrint` |
| `GenerateQrSvgAction` | `execute(string $payload, int $sizePx = 256): string` |
| `BuildLabelUrlAction` | `execute(string $code): string` → `{APP_URL}/l/{code}` (mai l'Host della richiesta) |
| `ResolveLabelTemplateAction` | `execute(Model&LabelableContract $model): LabelTemplate` |
| `ResolveDefaultPrinterAction` | `execute(LabelFormatEnum $format): ?Printer` |
| `EnsurePrinterAddressAllowedAction` | `execute(?string $host, int $port): string` (IP a cui collegarsi; form stampante e driver ZPL) |
| `GetLabelableResourceUrlAction` | `execute(Model $model): ?string` (pagina view/edit Filament, cercata in tutti i pannelli) |

Per l'invio asincrono: `app(PrintLabelAction::class)->onQueue()->execute($unit)`.

### Template

Il contenuto di un template si modifica dal pannello, quindi **non è mai codice**: è testo ZPL o HTML con segnaposto chiusi `{{ chiave }}`, sostituiti da `RenderLabelAction` con una sola passata (`preg_replace_callback`). Niente Blade, niente `@if`/`@foreach`, niente `{!! !!}`: prima eravamo esposti a esecuzione di codice sul server (audit di sicurezza C1).

| Segnaposto | Valore |
|---|---|
| `{{ code }}` | `getLabelCode()` |
| `{{ url }}` | payload del QR, `APP_URL/l/{code}` |
| `{{ width_mm }}`, `{{ height_mm }}`, `{{ dpi }}` | dimensioni e risoluzione del template |
| `{{ width_dots }}`, `{{ height_dots }}` | dimensioni in punti stampante |
| `{{ data.<chiave> }}` | un valore di `getLabelData()`; vuoto se il modello non ha quella chiave |
| `{{ line_1 }}` … `{{ line_N }}` | i valori di `getLabelData()` nell'ordine (template generici) |
| `{{ qr_svg }}` | solo PDF: SVG del QR, inserito senza escape |

Regole (`ValidateLabelTemplateContentAction`, usata dal form e prima di ogni render):
- rifiutati `<?`, `{!! !!}`, le direttive (`@php`, `@foreach`, …; un'email come `info@impianto.it` va bene) e ogni `{{ … }}` che non sia una chiave della tabella;
- nei PDF rifiutati `<img>`, `<link>`, `<script>`, `<iframe>`, `src=`, `href=`, `url(`: Html2Pdf caricherebbe file locali o URL dal server. L'unica immagine è `{{ qr_svg }}`;
- un template non valido già salvato fa fallire la stampa con il motivo (`LabelRenderException`), non stampa testo a caso.

Escape: nei PDF ogni valore passa da `htmlspecialchars`; nei ZPL i valori restano come sono (un `&` resta `&`) ma `^` e `~` diventano spazi, così un dato non inietta comandi nella stampante. Un valore che contiene `{{ x }}` resta testo. Il QR in ZPL si stampa con `^BQN,2,6^FDQA,{{ url }}^FS`.

Il seeder installa due template generici 100×50 mm (ZPL e PDF) con `{{ line_1 }}`…`{{ line_4 }}`; rieseguirlo (`updateOrCreate`) riscrive i default salvati con la vecchia sintassi Blade. I template personalizzati con Blade vanno riscritti a mano: finché non lo sono la stampa fallisce con il segnaposto non riconosciuto.

### Stampa

1. Il template è quello passato, altrimenti il default della classe, altrimenti il default generico.
2. La stampante è quella passata, altrimenti `config('labeling.default_printers.{zpl|pdf}')`. Deve essere attiva e compatibile con il formato del template.
3. Se c'è una stampante, il suo driver consegna l'etichetta:
   - `ZplTcpPrinterDriver` controlla l'indirizzo con `EnsurePrinterAddressAllowedAction`, usa un socket TCP raw verso l'IP controllato (timeout da config) e ripete il job per il numero di copie. Lo stato diventa `sent`.
   - `PdfDownloadPrinterDriver` usa Html2Pdf con il formato del template in mm e una pagina per copia, e salva il file sul disco `labeling.pdf.disk`. Lo stato diventa `rendered` e il path va in `output_path`.
4. Senza stampante un template PDF produce comunque il file. Un template ZPL senza stampante **fallisce**: `PrinterNotConfiguredException`, stampa `failed` con il motivo. Prima restava `rendered` in silenzio e l'operatore leggeva "etichetta in stampa" senza che uscisse nulla.
5. Ogni errore dopo la scelta del template (template non valido, nessuna stampante, stampante irraggiungibile o non consentita, PDF non scritto) salva la stampa come `failed` con il motivo in `error` e rilancia l'eccezione di dominio (`app/Exceptions`). Le azioni Filament la mostrano come notifica di errore; una stampa in coda finisce fra i job falliti e resta visibile nello storico `LabelPrintResource`.

Stati di `LabelPrint`: `queued` → `sent` (ZPL inviato), `rendered` (PDF pronto da scaricare) oppure `failed`.

### Scansione

`ResolveIdentifiableAction` normalizza la lettura:
- toglie i caratteri di controllo dei lettori keyboard-wedge;
- estrae il codice dagli URL `…/l/{code}`;
- converte EPC/UID esadecimali in maiuscolo compatto, togliendo i separatori `:`, `-` e gli spazi.

Poi cerca tra gli identificatori attivi tramite `active_value`.

La route `GET /l/{code}` (nome `labeling.resolve`) reindirizza alla pagina di dettaglio Filament del modello risolto e risponde 404 se il codice non è attivo. È pubblica (il QR si apre con la fotocamera del telefono), quindi ha `throttle` (`labeling.resolve_rate_limit` richieste al minuto per IP, default 30). Il QR è sempre costruito da `APP_URL`, mai dall'Host della richiesta.

## Filament

Il pannello è `labeling::admin` e contiene queste Resource:
- `LabelTemplateResource` (CRUD);
- `PrinterResource` (CRUD);
- `IdentifierResource` (lista e dettaglio, con revoca);
- `LabelPrintResource` (lista e dettaglio, con download del PDF e ristampa).

Le etichette arrivano dai file lang per classe: `{model}.php`, `{model}_form.php`, `{model}_infolist.php`, `{models}.php` ed enum `*_enum.php`, in it ed en.

## Punti di estensione (config `labeling`)

- `labelables`: le classi proposte nei template.
- `default_printers.zpl|pdf`: il codice della stampante di default (env `LABELING_DEFAULT_ZPL_PRINTER`, `LABELING_DEFAULT_PDF_PRINTER`, lette via `config('env.*')` così reggono `config:cache`).
- `zpl_tcp.connect_timeout|write_timeout`: i timeout del socket verso le stampanti ZPL.
- `allowed_printer_hosts` (env `LABELING_ALLOWED_PRINTER_HOSTS`, separati da virgola, letta via `config('env.*')`): indirizzi a cui il server può collegarsi per stampare. Vuota = solo IP privati di LAN (10/8, 172.16/12, 192.168/16, fc00::/7) e hostname che si risolvono lì. Valorizzata = solo IP, CIDR, hostname esatti o `*.dominio` elencati. Metadata cloud (169.254.169.254, 100.100.100.200, fd00:ec2::254), link-local, 0.0.0.0/8 e multicast sono sempre vietati.
- `printer_ports.min|max`: intervallo di porte ammesse (default 1024–65535: niente SSH, SMTP, HTTP…).
- `pdf.disk|directory`: dove salvare i PDF.
- `qr_size_px`: il lato in pixel del QR nei PDF.
- `max_copies`: il numero massimo di copie per stampa.
- `resolve_rate_limit`: richieste al minuto per IP su `GET /l/{code}`.

## Requisiti del progetto

- La connessione database `labeling` la crea il modulo Tenant, copiando la connessione predefinita per ogni modulo attivo. Va definita a mano in `config/{tenant}/database.php` solo se il modulo deve usare un database diverso. In ambiente di test la connessione deve comunque esistere in `config/database.php` (se si usa un solo database, basta un alias della connessione principale): `tests/TestCase.php` la usa in `$connectionsToTransact`.
- Test: `tests/TestCase.php` usa lo sqlite condiviso di Xot e crea le tabelle mancanti con `XotModuleSchema::ensure('Labeling')`.
- Permessi Spatie usati dalle policy: `label_template.*`, `printer.*`, `identifier.*` (`viewAny`, `view`, `create`, `update`, `delete`) e `label_print.*` (`viewAny`, `view`, `create`, `delete`). Li crea `LabelingPermissionSeeder` (idempotente, guard `web`, richiamato da `LabelingDatabaseSeeder`) insieme al ruolo `labeling::admin` (nome = id del pannello) con tutti i permessi del modulo. Senza permessi seminati `hasPermissionTo()` lancia `PermissionDoesNotExist` (errore 500 per chi non è super-admin).

## Autorizzazione

- Tutte le policy usano `DeniesUndeclaredFilamentAbilities` (Xot): `deleteAny`, `forceDeleteAny`, `restoreAny`, `reorder`, `replicate`, `attach`, `detach`, … sono negati se la policy non li dichiara.
- Le azioni custom controllano un'abilità, non solo lo stato del record:

| Azione | Abilità | Permesso |
|---|---|---|
| Stampa etichetta (`PrintLabelTableAction`, `PrintLabelHeaderAction`, su qualunque modello) | `create` su `LabelPrint` (anche dentro l'azione, prima di stampare) | `label_print.create` |
| Ristampa (storico stampe) | `create` | `label_print.create` |
| Scarica PDF | `view` | `label_print.view` |
| Revoca identificatore | `update` | `identifier.update` |

- Stampa e ristampa hanno `rateLimit` (`labeling.print_rate_limit`, default 20 al minuto per IP e azione).
