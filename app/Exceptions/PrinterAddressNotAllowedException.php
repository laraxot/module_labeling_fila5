<?php

declare(strict_types=1);

namespace Modules\Labeling\Exceptions;

/**
 * Host o porta di una stampante fuori da quanto consentito
 * ({@see \Modules\Labeling\Actions\EnsurePrinterAddressAllowedAction}). Il messaggio serve anche come
 * errore di validazione del form, quindi non nomina la stampante.
 */
class PrinterAddressNotAllowedException extends LabelingException
{
    public static function missingHost(): self
    {
        return self::make('missing_host');
    }

    public static function invalidHost(string $host): self
    {
        return self::make('invalid_host', ['host' => $host]);
    }

    public static function unresolvable(string $host): self
    {
        return self::make('unresolvable', ['host' => $host]);
    }

    public static function notAllowed(string $host): self
    {
        return self::make('not_allowed', ['host' => $host]);
    }

    public static function port(int $port, int $min, int $max): self
    {
        return self::make('port', ['port' => (string) $port, 'min' => (string) $min, 'max' => (string) $max]);
    }

    /**
     * @param  array<string, string>  $replace
     */
    private static function make(string $key, array $replace = []): self
    {
        return new self((string) __('labeling::exceptions.printer_address.'.$key, $replace));
    }
}
