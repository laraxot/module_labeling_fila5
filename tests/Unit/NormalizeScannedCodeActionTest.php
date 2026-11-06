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

    test('extracts the code from the QR url', function (): void {
        $cases = [
            'https://roosko.example.it/l/SU-2026-000042' => 'SU-2026-000042',
            'https://roosko.example.it/l/SU-2026-000042/?utm=x' => 'SU-2026-000042',
            '/l/LOC-A-01' => 'LOC-A-01',
            'https://host/l/A%20B' => 'A B',
        ];

        foreach ($cases as $scanned => $expected) {
            $scan = app(NormalizeScannedCodeAction::class)->execute($scanned);

            Assert::assertSame($expected, $scan->code, $scanned);
            Assert::assertTrue($scan->fromUrl, $scanned);
            Assert::assertNull($scan->hex, $scanned);
        }
    });

    test('normalizes hexadecimal RFID and NFC values to compact uppercase', function (): void {
        $cases = [
            'e28011606000020a1b2c3d4e' => 'E28011606000020A1B2C3D4E',
            '04:a2:1b:3c:4d:5e:80' => '04A21B3C4D5E80',
            '04-A2-1B-3C' => '04A21B3C',
        ];

        foreach ($cases as $scanned => $expected) {
            Assert::assertSame($expected, app(NormalizeScannedCodeAction::class)->execute($scanned)->hex, $scanned);
        }
    });

    test('does not treat short or non hexadecimal codes as hex', function (): void {
        foreach (['ABC123', 'SU-2026-000042', 'E2801160Z', 'ABCDEF1'] as $scanned) {
            Assert::assertNull(app(NormalizeScannedCodeAction::class)->execute($scanned)->hex, $scanned);
        }
    });

    test('reports an empty reading', function (): void {
        Assert::assertTrue(app(NormalizeScannedCodeAction::class)->execute(" \r\n")->isEmpty());
    });
});
