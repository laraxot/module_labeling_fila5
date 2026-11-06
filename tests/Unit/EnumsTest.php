<?php

declare(strict_types=1);

use Modules\Labeling\Drivers\PdfDownloadPrinterDriver;
use Modules\Labeling\Drivers\ZplTcpPrinterDriver;
use Modules\Labeling\Enums\IdentifierTypeEnum;
use Modules\Labeling\Enums\LabelFormatEnum;
use Modules\Labeling\Enums\LabelPrintStatusEnum;
use Modules\Labeling\Enums\PrinterDriverEnum;
use Modules\Labeling\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

describe('LabelPrintStatusEnum', function (): void {
    test('allows only the documented transitions', function (): void {
        $cases = [
        [LabelPrintStatusEnum::QUEUED, LabelPrintStatusEnum::SENT, true],
        [LabelPrintStatusEnum::QUEUED, LabelPrintStatusEnum::RENDERED, true],
        [LabelPrintStatusEnum::QUEUED, LabelPrintStatusEnum::FAILED, true],
        [LabelPrintStatusEnum::RENDERED, LabelPrintStatusEnum::SENT, true],
        [LabelPrintStatusEnum::SENT, LabelPrintStatusEnum::QUEUED, false],
        [LabelPrintStatusEnum::FAILED, LabelPrintStatusEnum::SENT, false],
        [LabelPrintStatusEnum::RENDERED, LabelPrintStatusEnum::QUEUED, false],
        ];

        foreach ($cases as [$from, $to, $allowed]) {
            Assert::assertSame($allowed, $from->canTransitionTo($to), $from->value.' -> '.$to->value);
        }
    });

    test('has a translated label for every case', function (): void {
        foreach (LabelPrintStatusEnum::cases() as $case) {
            Assert::assertStringNotContainsString('fix:', $case->getLabel());
        }
    });
});

describe('PrinterDriverEnum', function (): void {
    test('maps each driver to its class and template format', function (): void {
        Assert::assertSame(ZplTcpPrinterDriver::class, PrinterDriverEnum::ZPL_TCP->driverClass());
        Assert::assertSame(PdfDownloadPrinterDriver::class, PrinterDriverEnum::PDF_DOWNLOAD->driverClass());
        Assert::assertSame(LabelFormatEnum::ZPL, PrinterDriverEnum::ZPL_TCP->format());
        Assert::assertSame(LabelFormatEnum::PDF, PrinterDriverEnum::PDF_DOWNLOAD->format());
    });
});

describe('IdentifierTypeEnum', function (): void {
    test('keeps the values fixed by the cross-module contract', function (): void {
        Assert::assertSame(['qr', 'barcode128', 'rfid_uhf', 'nfc'], array_map(
            static fn (IdentifierTypeEnum $type): string => $type->value,
            IdentifierTypeEnum::cases(),
        ));
        Assert::assertTrue(IdentifierTypeEnum::QR->isPrinted());
        Assert::assertTrue(IdentifierTypeEnum::RFID_UHF->isHexadecimal());
    });
});
