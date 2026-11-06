<?php

declare(strict_types=1);

namespace Modules\Labeling\Actions;

use Modules\Labeling\Exceptions\PrinterAddressNotAllowedException;
use Spatie\QueueableAction\QueueableAction;

use function Safe\inet_pton;
use function Safe\preg_match;

/**
 * Indirizzo di rete di una stampante ZPL: controlla host e porta e restituisce l'IP a cui collegarsi.
 * Usata dal form della stampante e da {@see \Modules\Labeling\Drivers\ZplTcpPrinterDriver} prima di
 * aprire il socket, cosi' il server non diventa un proxy verso la propria rete o il cloud (SSRF).
 *
 * - host non vuoto: IP (v4/v6) o hostname sintatticamente valido, mai `localhost`; gli hostname si
 *   risolvono (IPv4) e si controllano tutti gli indirizzi, poi ci si collega al primo (niente DNS
 *   rebinding fra controllo e connessione);
 * - porta in `labeling.printer_ports` (min/max);
 * - sempre negati: metadata cloud, link-local, 0.0.0.0/8, multicast/broadcast;
 * - `labeling.allowed_printer_hosts` vuota: solo indirizzi privati di LAN (10/8, 172.16/12,
 *   192.168/16, fc00::/7), quindi niente loopback ne' indirizzi pubblici;
 * - `labeling.allowed_printer_hosts` valorizzata: solo gli IP/CIDR o hostname elencati
 *   (`*.dominio` copre i sottodomini), anche loopback se elencato esplicitamente.
 *
 * @throws PrinterAddressNotAllowedException
 */
class EnsurePrinterAddressAllowedAction
{
    use QueueableAction;

    /** Reti mai raggiungibili, nemmeno se elencate: metadata cloud, link-local, "questa rete", multicast, riservate. */
    private const array DENIED_NETWORKS = [
        '0.0.0.0/8',
        '169.254.0.0/16',
        '100.100.100.200/32',
        '224.0.0.0/4',
        '240.0.0.0/4',
        '::/128',
        'fe80::/10',
        'ff00::/8',
        'fd00:ec2::254/128',
    ];

    /** Reti private di LAN, ammesse quando non c'e' una lista esplicita. */
    private const array PRIVATE_NETWORKS = [
        '10.0.0.0/8',
        '172.16.0.0/12',
        '192.168.0.0/16',
        'fc00::/7',
    ];

    public function execute(?string $host, int $port): string
    {
        $host = trim((string) $host);
        $host = str_starts_with($host, '[') && str_ends_with($host, ']') ? substr($host, 1, -1) : $host;
        if ($host === '') {
            throw PrinterAddressNotAllowedException::missingHost();
        }

        $min = config()->integer('labeling.printer_ports.min', 1024);
        $max = config()->integer('labeling.printer_ports.max', 65535);
        if ($port < $min || $port > $max) {
            throw PrinterAddressNotAllowedException::port($port, $min, $max);
        }

        /** @var list<string> $allowed */
        $allowed = array_values(array_filter(
            array_map(static fn (mixed $entry): string => is_string($entry) ? strtolower(trim($entry)) : '', config()->array('labeling.allowed_printer_hosts', [])),
            static fn (string $entry): bool => $entry !== '',
        ));

        $isIp = self::pack($host) !== null;
        $nameAllowed = false;
        if (! $isIp) {
            $host = strtolower($host);
            if (! self::isHostname($host)) {
                throw PrinterAddressNotAllowedException::invalidHost($host);
            }
            if ($host === 'localhost' || str_ends_with($host, '.localhost')) {
                throw PrinterAddressNotAllowedException::notAllowed($host);
            }
            $nameAllowed = self::hostnameListed($host, $allowed);
        }

        $ips = $isIp ? [$host] : (gethostbynamel($host) ?: []);
        if ($ips === []) {
            throw PrinterAddressNotAllowedException::unresolvable($host);
        }

        foreach ($ips as $ip) {
            $packed = self::pack($ip);
            if ($packed === null || self::inAny($packed, self::DENIED_NETWORKS)) {
                throw PrinterAddressNotAllowedException::notAllowed($host);
            }

            $permitted = $allowed === []
                ? self::inAny($packed, self::PRIVATE_NETWORKS)
                : ($nameAllowed || self::inAny($packed, $allowed));
            if (! $permitted) {
                throw PrinterAddressNotAllowedException::notAllowed($host);
            }
        }

        return $ips[0];
    }

    /** Hostname RFC 1123; l'ultima etichetta non puo' essere numerica ne' `0x..` (forme abbreviate di IP). */
    private static function isHostname(string $host): bool
    {
        if (filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) === false) {
            return false;
        }

        $labels = explode('.', rtrim($host, '.'));
        $last = end($labels);

        return preg_match('/^(?:\d+|0x[0-9a-f]*)$/i', $last) !== 1;
    }

    /**
     * @param  list<string>  $allowed
     */
    private static function hostnameListed(string $host, array $allowed): bool
    {
        foreach ($allowed as $entry) {
            if ($entry === $host || (str_starts_with($entry, '*.') && str_ends_with($host, substr($entry, 1)))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  list<string>  $networks  IP o CIDR; le voci che non sono indirizzi si ignorano
     */
    private static function inAny(string $packed, array $networks): bool
    {
        foreach ($networks as $network) {
            $parts = explode('/', $network, 2);
            $net = self::pack($parts[0]);
            $bits = $parts[1] ?? null;
            if ($net === null || strlen($net) !== strlen($packed)) {
                continue;
            }

            $bits = $bits === null || ! ctype_digit($bits) ? strlen($net) * 8 : min((int) $bits, strlen($net) * 8);
            $bytes = intdiv($bits, 8);
            if (strncmp($packed, $net, $bytes) !== 0) {
                continue;
            }

            $rest = $bits % 8;
            if ($rest === 0) {
                return true;
            }

            $mask = (0xFF << (8 - $rest)) & 0xFF;
            if ((ord($packed[$bytes]) & $mask) === (ord($net[$bytes]) & $mask)) {
                return true;
            }
        }

        return false;
    }

    /** Indirizzo in forma binaria; un IPv4 mappato in IPv6 (`::ffff:a.b.c.d`) torna IPv4. */
    private static function pack(string $ip): ?string
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return null;
        }

        $packed = inet_pton($ip);

        return strlen($packed) === 16 && str_starts_with($packed, str_repeat("\0", 10)."\xff\xff")
            ? substr($packed, 12)
            : $packed;
    }
}
