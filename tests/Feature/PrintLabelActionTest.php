<?php

declare(strict_types=1);

use Modules\Labeling\Actions\PrintLabelAction;
use Modules\Labeling\Enums\LabelPrintStatusEnum;
use Modules\Labeling\Exceptions\IncompatiblePrinterException;
use Modules\Labeling\Exceptions\LabelRenderException;
use Modules\Labeling\Exceptions\LabelTemplateNotFoundException;
use Modules\Labeling\Exceptions\PrinterAddressNotAllowedException;
use Modules\Labeling\Exceptions\PrinterConnectionException;
use Modules\Labeling\Exceptions\PrinterNotConfiguredException;
use Modules\Labeling\Models\LabelPrint;
use Modules\Labeling\Models\LabelTemplate;
use Modules\Labeling\Models\Printer;
use Modules\Labeling\Tests\Fixtures\LabelableItem;
use Modules\Labeling\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\fclose;
use function Safe\stream_socket_get_name;
use function Safe\stream_socket_server;

uses(TestCase::class);

/** Una porta TCP locale sicuramente chiusa: aperta e richiusa subito. */
function labelingClosedPort(): int
{
    $server = stream_socket_server('tcp://127.0.0.1:0');
    $name = stream_socket_get_name($server, false);
    fclose($server);

    return (int) substr($name, strrpos($name, ':') + 1);
}

beforeEach(function (): void {
    config([
        'labeling.default_printers' => ['zpl' => null, 'pdf' => null],
        'labeling.allowed_printer_hosts' => [],
    ]);
});

describe('PrintLabelAction', function (): void {
    test('fails visibly when a ZPL label has no printer, keeping the snapshot and the reason', function (): void {
        $item = LabelableItem::createWith('SU-P-0001', 'Big bag');
        $template = LabelTemplate::factory()->forType(LabelableItem::class)->withContent('^XA^FD{{ code }}^FS^XZ')->create();

        try {
            app(PrintLabelAction::class)->execute($item, copies: 2);
            Assert::fail('PrinterNotConfiguredException expected');
        } catch (PrinterNotConfiguredException $e) {
            Assert::assertStringContainsString($template->code, $e->getMessage());
        }

        $print = $item->labelPrints()->sole();
        Assert::assertInstanceOf(LabelPrint::class, $print);
        Assert::assertSame(LabelPrintStatusEnum::FAILED, $print->status);
        Assert::assertSame('^XA^FDSU-P-0001^FS^XZ', $print->rendered);
        Assert::assertSame($template->id, $print->label_template_id);
        Assert::assertSame(2, $print->copies);
        Assert::assertNotNull($print->error);
        Assert::assertNull($print->printed_at);
    });

    test('records a failed print when the template content is not valid', function (): void {
        $item = LabelableItem::createWith('SU-P-0006');
        $template = LabelTemplate::factory()->withContent('^XA@foreach ($data as $v)^FD{{ $v }}^FS@endforeach^XZ')->create();

        try {
            app(PrintLabelAction::class)->execute($item, $template);
            Assert::fail('LabelRenderException expected');
        } catch (LabelRenderException) {
        }

        $print = LabelPrint::query()->where('labelable_id', $item->id)->sole();
        Assert::assertSame(LabelPrintStatusEnum::FAILED, $print->status);
        Assert::assertSame('', $print->rendered);
        Assert::assertNotNull($print->error);
    });

    test('marks the print failed and rethrows when the printer is unreachable', function (): void {
        config(['labeling.allowed_printer_hosts' => ['127.0.0.1']]);
        $item = LabelableItem::createWith('SU-P-0002');
        $template = LabelTemplate::factory()->withContent('^XA^FD{{ code }}^FS^XZ')->create();
        $printer = Printer::factory()->at('127.0.0.1', labelingClosedPort())->create();

        try {
            app(PrintLabelAction::class)->execute($item, $template, $printer);
            Assert::fail('PrinterConnectionException expected');
        } catch (PrinterConnectionException $e) {
            Assert::assertStringNotContainsStringIgnoringCase('refused', $e->getMessage());
        }

        $print = LabelPrint::query()->where('labelable_id', $item->id)->sole();
        Assert::assertSame(LabelPrintStatusEnum::FAILED, $print->status);
        Assert::assertNotNull($print->error);
        Assert::assertNull($print->printed_at);
    });

    test('never opens a socket towards loopback or cloud metadata addresses', function (string $host): void {
        $item = LabelableItem::createWith('SU-P-0007');
        $template = LabelTemplate::factory()->withContent('^XA^FD{{ code }}^FS^XZ')->create();
        $printer = Printer::factory()->at($host, 9100)->create();

        try {
            app(PrintLabelAction::class)->execute($item, $template, $printer);
            Assert::fail('PrinterAddressNotAllowedException expected');
        } catch (PrinterAddressNotAllowedException) {
        }

        Assert::assertSame(LabelPrintStatusEnum::FAILED, LabelPrint::query()->where('labelable_id', $item->id)->sole()->status);
    })->with(['127.0.0.1', '169.254.169.254', 'localhost', '8.8.8.8']);

    test('refuses a printer whose driver does not match the template format', function (): void {
        $item = LabelableItem::createWith('SU-P-0003');
        $template = LabelTemplate::factory()->create();
        $printer = Printer::factory()->pdf()->create();

        app(PrintLabelAction::class)->execute($item, $template, $printer);
    })->throws(IncompatiblePrinterException::class);

    test('fails clearly when no template is available', function (): void {
        LabelTemplate::query()->update(['is_active' => false]);

        app(PrintLabelAction::class)->execute(LabelableItem::createWith('SU-P-0004'));
    })->throws(LabelTemplateNotFoundException::class);

    test('rejects an out of range number of copies', function (): void {
        app(PrintLabelAction::class)->execute(LabelableItem::createWith('SU-P-0005'), copies: 0);
    })->throws(InvalidArgumentException::class);
});
