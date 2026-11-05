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
| `LabelTemplate` | `label_templates` | `content` Blade; default per classe (`labelable_type` + `is_default`) o generico (`labelable_type` null) |
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
| `PrintLabelAction` (queueable) | `execute(Model&LabelableContract $model, ?LabelTemplate $template = null, ?Printer $printer = null, int $copies = 1): LabelPrint` |
| `GenerateQrSvgAction` | `execute(string $payload, int $sizePx = 256): string` |
| `BuildLabelUrlAction` | `execute(string $code): string` → `url('/l/{code}')` |
| `ResolveLabelTemplateAction` | `execute(Model&LabelableContract $model): LabelTemplate` |
| `ResolveDefaultPrinterAction` | `execute(LabelFormatEnum $format): ?Printer` |
| `GetLabelableResourceUrlAction` | `execute(Model $model): ?string` (pagina view/edit Filament, cercata in tutti i pannelli) |

Per l'invio asincrono: `app(PrintLabelAction::class)->onQueue()->execute($unit)`.

### Template

Sono Blade e ricevono queste variabili: `$code`, `$url` (payload del QR), `$data` (`getLabelData()`), `$widthMm`, `$heightMm`, `$dpi`, `$widthDots`, `$heightDots`. I PDF ricevono anche `$qrSvg`, da stampare con `{!! $qrSvg !!}`.

Nei template ZPL `{{ }}` stampa senza escape HTML. I caratteri di comando `^` e `~` presenti nei dati del modello vengono sostituiti con uno spazio. Il QR in ZPL si stampa con `^BQN,2,6^FDQA,{{ $url }}^FS`. Il seeder installa due template generici 100×50 mm (ZPL e PDF).

### Stampa

1. Il template è quello passato, altrimenti il default della classe, altrimenti il default generico.
2. La stampante è quella passata, altrimenti `config('labeling.default_printers.{zpl|pdf}')`. Deve essere attiva e compatibile con il formato del template.
3. Se c'è una stampante, il suo driver consegna l'etichetta:
   - `ZplTcpPrinterDriver` usa un socket TCP raw (timeout da config) e ripete il job per il numero di copie. Lo stato diventa `sent`.
   - `PdfDownloadPrinterDriver` usa Html2Pdf con il formato del template in mm e una pagina per copia, e salva il file sul disco `labeling.pdf.disk`. Lo stato diventa `rendered` e il path va in `output_path`.
4. Senza stampante, un template PDF produce comunque il file, mentre un template ZPL resta `rendered`.
5. Un errore di consegna salva la stampa come `failed` con il motivo in `error` e rilancia l'eccezione di dominio (`app/Exceptions`).

### Scansione

`ResolveIdentifiableAction` normalizza la lettura:
- toglie i caratteri di controllo dei lettori keyboard-wedge;
- estrae il codice dagli URL `…/l/{code}`;
- converte EPC/UID esadecimali in maiuscolo compatto, togliendo i separatori `:`, `-` e gli spazi.

Poi cerca tra gli identificatori attivi tramite `active_value`.

La route `GET /l/{code}` (nome `labeling.resolve`) reindirizza alla pagina di dettaglio Filament del modello risolto e risponde 404 se il codice non è attivo.

## Filament

Il pannello è `labeling::admin` e contiene queste Resource:
- `LabelTemplateResource` (CRUD);
- `PrinterResource` (CRUD);
- `IdentifierResource` (lista e dettaglio, con revoca);
- `LabelPrintResource` (lista e dettaglio, con download del PDF e ristampa).

Le etichette arrivano dai file lang per classe: `{model}.php`, `{model}_form.php`, `{model}_infolist.php`, `{models}.php` ed enum `*_enum.php`, in it ed en.

## Punti di estensione (config `labeling`)

- `labelables`: le classi proposte nei template.
- `default_printers.zpl|pdf`: il codice della stampante di default (env `LABELING_DEFAULT_ZPL_PRINTER`, `LABELING_DEFAULT_PDF_PRINTER`).
- `zpl_tcp.connect_timeout|write_timeout`: i timeout del socket verso le stampanti ZPL.
- `pdf.disk|directory`: dove salvare i PDF.
- `qr_size_px`: il lato in pixel del QR nei PDF.
- `max_copies`: il numero massimo di copie per stampa.

## Requisiti del progetto

- La connessione database `labeling` la crea il modulo Tenant, copiando la connessione predefinita per ogni modulo attivo. Va definita a mano in `config/{tenant}/database.php` solo se il modulo deve usare un database diverso.
- Test: `tests/TestCase.php` usa lo sqlite condiviso di Xot e crea le tabelle mancanti con `XotModuleSchema::ensure('Labeling')`.
- Permessi Spatie usati dalle policy: `label_template.*`, `printer.*`, `identifier.*`, `label_print.*` (`viewAny`, `view`, `create`, `update`, `delete`).
