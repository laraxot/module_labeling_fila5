<?php

declare(strict_types=1);

namespace Modules\Labeling\Drivers;

use Illuminate\Support\Facades\Log;
use Modules\Labeling\Actions\EnsurePrinterAddressAllowedAction;
use Modules\Labeling\Contracts\PrinterDriverContract;
use Modules\Labeling\Datas\PrinterDeliveryData;
use Modules\Labeling\Enums\LabelPrintStatusEnum;
use Modules\Labeling\Exceptions\PrinterConnectionException;
use Modules\Labeling\Exceptions\PrinterTimeoutException;
use Modules\Labeling\Models\LabelPrint;
use Modules\Labeling\Models\Printer;

use function Safe\fclose;
use function Safe\fflush;
use function Safe\fwrite;
use function Safe\stream_set_timeout;
use function Safe\stream_socket_client;

/**
 * Invia il ZPL alla stampante via socket TCP raw (Zebra & compatibili, porta 9100).
 * Le copie si ottengono ripetendo il job (`^XA...^XZ`), indipendente dal contenuto del template.
 * Timeout di connessione e di scrittura da `config('labeling.zpl_tcp')`.
 *
 * Prima di aprire il socket l'indirizzo passa da {@see EnsurePrinterAddressAllowedAction} (rete locale o
 * lista consentita, porta nell'intervallo): ci si collega all'IP controllato, non al nome.
 * L'errore del socket va nel log, non nel messaggio all'operatore.
 */
class ZplTcpPrinterDriver implements PrinterDriverContract
{
    public function deliver(LabelPrint $print): PrinterDeliveryData
    {
        $printer = $print->printer;
        if (! $printer instanceof Printer) {
            throw new \LogicException('ZplTcpPrinterDriver requires a printer on the label print.');
        }

        $host = $printer->host;
        if ($host === null || trim($host) === '') {
            throw PrinterConnectionException::missingHost($printer);
        }

        $ip = app(EnsurePrinterAddressAllowedAction::class)->execute($host, $printer->port);

        $connectTimeout = config()->float('labeling.zpl_tcp.connect_timeout', 3.0);
        $writeTimeout = config()->float('labeling.zpl_tcp.write_timeout', 5.0);
        $payload = str_repeat($print->rendered, max(1, $print->copies));

        $address = sprintf('tcp://%s:%d', str_contains($ip, ':') ? '['.$ip.']' : $ip, $printer->port);
        $errno = 0;
        $errstr = '';
        try {
            $socket = stream_socket_client($address, $errno, $errstr, $connectTimeout);
        } catch (\Throwable $e) {
            Log::warning('Labeling: stampante non raggiungibile', [
                'printer' => $printer->code,
                'address' => $address,
                'errno' => $errno,
                'error' => $errstr,
                'exception' => $e->getMessage(),
            ]);

            throw PrinterConnectionException::unreachable($printer, $errno ?? 0);
        }

        $written = 0;
        try {
            $seconds = (int) floor($writeTimeout);
            stream_set_timeout($socket, $seconds, (int) round(($writeTimeout - $seconds) * 1_000_000));

            $length = strlen($payload);
            while ($written < $length) {
                $chunk = fwrite($socket, substr($payload, $written));
                if (stream_get_meta_data($socket)['timed_out']) {
                    throw PrinterTimeoutException::whileWriting($printer, $writeTimeout);
                }
                if ($chunk === 0) {
                    throw PrinterConnectionException::writeFailed($printer);
                }
                $written += $chunk;
            }
            fflush($socket);
        } finally {
            fclose($socket);
        }

        return new PrinterDeliveryData(status: LabelPrintStatusEnum::SENT, bytes: $written);
    }
}
