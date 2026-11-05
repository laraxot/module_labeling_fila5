<?php

declare(strict_types=1);

use Modules\Labeling\Actions\NormalizeScannedCodeAction;
use Modules\Labeling\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

describe('NormalizeScannedCodeAction', function (): void {
    test('strips keyboard-wedge control characters and spaces', function (): void {
        $scan = app(NormalizeScannedCodeAction::class)->execute("\x02  SU-2026-000042\r\n");

        Assert::assertSame('SU-2026-000042', $scan->code);
        Assert::assertFalse($scan->fromUrl);
        Assert::assertNull($scan->hex);
    });

    test('extracts the code from the QR url', function (string $scanned, string $expected): void {
        $scan = app(NormalizeScannedCodeAction::class)->execute($scanned);

        Assert::assertSame($expected, $scan->code);
        Assert::assertTrue($scan->fromUrl);
        Assert::assertNull($scan->hex);
    })->with([
        'absolute' => ['https://roosko.example.it/l/SU-2026-000042', 'SU-2026-000042'],
        'trailing slash and query' => ['https://roosko.example.it/l/SU-2026-000042/?utm=x', 'SU-2026-000042'],
        'relative' => ['/l/LOC-A-01', 'LOC-A-01'],
        'encoded' => ['https://host/l/A%20B', 'A B'],
    ]);

    test('normalizes hexadecimal RFID and NFC values to compact uppercase', function (string $scanned, string $expected): void {
        Assert::assertSame($expected, app(NormalizeScannedCodeAction::class)->execute($scanned)->hex);
    })->with([
        'epc lowercase' => ['e28011606000020a1b2c3d4e', 'E28011606000020A1B2C3D4E'],
        'nfc with colons' => ['04:a2:1b:3c:4d:5e:80', '04A21B3C4D5E80'],
        'nfc with dashes' => ['04-A2-1B-3C', '04A21B3C'],
    ]);

    test('does not treat short or non hexadecimal codes as hex', function (string $scanned): void {
        Assert::assertNull(app(NormalizeScannedCodeAction::class)->execute($scanned)->hex);
    })->with(['ABC123', 'SU-2026-000042', 'E2801160Z', 'ABCDEF1']);

    test('reports an empty reading', function (): void {
        Assert::assertTrue(app(NormalizeScannedCodeAction::class)->execute(" \r\n")->isEmpty());
    });
});
