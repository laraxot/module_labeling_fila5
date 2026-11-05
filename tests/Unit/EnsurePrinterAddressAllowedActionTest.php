<?php

declare(strict_types=1);

use Modules\Labeling\Actions\EnsurePrinterAddressAllowedAction;
use Modules\Labeling\Exceptions\PrinterAddressNotAllowedException;
use Modules\Labeling\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

beforeEach(function (): void {
    config([
        'labeling.allowed_printer_hosts' => [],
        'labeling.printer_ports' => ['min' => 1024, 'max' => 65535],
    ]);
});

describe('EnsurePrinterAddressAllowedAction', function (): void {
    test('accepts private LAN addresses when no allowlist is configured', function (string $host, string $expected): void {
        Assert::assertSame($expected, app(EnsurePrinterAddressAllowedAction::class)->execute($host, 9100));
    })->with([
        ['192.168.1.50', '192.168.1.50'],
        ['10.0.0.5', '10.0.0.5'],
        ['172.16.3.4', '172.16.3.4'],
        ['[fd12::1]', 'fd12::1'],
    ]);

    test('rejects loopback, link-local, metadata, public and short-form addresses', function (string $host): void {
        app(EnsurePrinterAddressAllowedAction::class)->execute($host, 9100);
    })->throws(PrinterAddressNotAllowedException::class)->with([
        '127.0.0.1', '::1', '::ffff:127.0.0.1', '169.254.169.254', 'fd00:ec2::254', '0.0.0.0',
        'localhost', 'printer.localhost', '2130706433', '0x7f000001', '127.1', '8.8.8.8', '', '   ',
    ]);

    test('rejects ports outside the configured range', function (int $port): void {
        app(EnsurePrinterAddressAllowedAction::class)->execute('192.168.1.50', $port);
    })->throws(PrinterAddressNotAllowedException::class)->with([0, 22, 80, 70000]);

    test('with an allowlist only the listed hosts and networks pass, metadata never', function (): void {
        config(['labeling.allowed_printer_hosts' => ['127.0.0.1', '10.1.0.0/16']]);
        $action = app(EnsurePrinterAddressAllowedAction::class);

        Assert::assertSame('127.0.0.1', $action->execute('127.0.0.1', 9100));
        Assert::assertSame('10.1.2.3', $action->execute('10.1.2.3', 9100));

        foreach (['10.2.0.1', '192.168.1.50', '169.254.169.254'] as $host) {
            try {
                $action->execute($host, 9100);
                Assert::fail($host.' should be refused');
            } catch (PrinterAddressNotAllowedException) {
            }
        }
    });
});
