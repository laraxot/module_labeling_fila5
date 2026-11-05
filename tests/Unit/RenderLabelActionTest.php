<?php

declare(strict_types=1);

use chillerlan\QRCode\QRCode;
use Modules\Labeling\Actions\GenerateQrSvgAction;
use Modules\Labeling\Actions\RenderLabelAction;
use Modules\Labeling\Actions\ValidateLabelTemplateContentAction;
use Modules\Labeling\Database\Seeders\LabelTemplateSeeder;
use Modules\Labeling\Enums\LabelFormatEnum;
use Modules\Labeling\Exceptions\LabelRenderException;
use Modules\Labeling\Models\LabelTemplate;
use Modules\Labeling\Tests\Fixtures\LabelableItem;
use Modules\Labeling\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

function labelingTemplate(LabelFormatEnum $format, string $content): LabelTemplate
{
    return new LabelTemplate([
        'code' => 'T-'.$format->value,
        'name' => 'Test',
        'format' => $format,
        'width_mm' => 100,
        'height_mm' => 50,
        'dpi' => 203,
        'content' => $content,
        'is_active' => true,
    ]);
}

beforeEach(function (): void {
    config(['app.url' => 'https://impianto.example']);
    unset($GLOBALS['labeling_pwned']);
});

describe('RenderLabelAction', function (): void {
    test('renders ZPL placeholders without HTML escaping and neutralizes ZPL command characters', function (): void {
        $item = new LabelableItem(['code' => 'SU-1', 'name' => 'Ferro & acciaio ^FS~JA']);
        $template = labelingTemplate(LabelFormatEnum::ZPL, '^XA^PW{{ width_dots }}^FD{{code}}^FS^FD{{ data.name }}^FS^FD{{ url }}^FS^FD{{ line_2 }}^FS^XZ');

        $zpl = app(RenderLabelAction::class)->execute($item, $template);

        Assert::assertSame('^XA^PW799^FDSU-1^FS^FDFerro & acciaio  FS JA^FS^FDhttps://impianto.example/l/SU-1^FS^FD12.5^FS^XZ', $zpl);
    });

    test('renders PDF HTML with escaped values and the inline QR svg', function (): void {
        if (! class_exists(QRCode::class)) {
            test()->markTestSkipped('chillerlan/php-qrcode non installato');
        }

        $item = new LabelableItem(['code' => 'SU-2', 'name' => '<b>x</b> "q"']);
        $template = labelingTemplate(LabelFormatEnum::PDF, '<div>{{ code }} {{ data.name }}</div>{{ qr_svg }}');

        $html = app(RenderLabelAction::class)->execute($item, $template);

        Assert::assertStringContainsString('<div>SU-2 &lt;b&gt;x&lt;/b&gt; &quot;q&quot;</div>', $html);
        Assert::assertStringContainsString('<svg width="256" height="256"', $html);
    });

    test('renders missing data keys as empty text and never re-expands values', function (): void {
        $item = new LabelableItem(['code' => 'SU-3', 'name' => '{{ code }}']);
        $template = labelingTemplate(LabelFormatEnum::ZPL, '^XA^FD{{ data.missing }}|{{ line_9 }}|{{ data.name }}^FS^XZ');

        $zpl = app(RenderLabelAction::class)->execute($item, $template);

        Assert::assertSame('^XA^FD||{{ code }}^FS^XZ', $zpl);
    });

    test('never executes Blade or PHP written in the template', function (string $content): void {
        $item = new LabelableItem(['code' => 'SU-4']);

        try {
            app(RenderLabelAction::class)->execute($item, labelingTemplate(LabelFormatEnum::ZPL, $content));
            Assert::fail('LabelRenderException expected');
        } catch (LabelRenderException) {
        }

        Assert::assertArrayNotHasKey('labeling_pwned', $GLOBALS);
    })->with([
        'php directive' => ["^XA@php \$GLOBALS['labeling_pwned'] = true; @endphp^XZ"],
        'raw echo' => ["^XA{!! \$GLOBALS['labeling_pwned'] = true !!}^XZ"],
        'expression echo' => ["^XA{{ \$GLOBALS['labeling_pwned'] = true }}^XZ"],
        'function call' => ['^XA{{ app()->environment() }}^XZ'],
        'php tag' => ["^XA<?php \$GLOBALS['labeling_pwned'] = true; ?>^XZ"],
        'foreach' => ['^XA@foreach ($data as $v)^FD{{ $v }}^FS@endforeach^XZ'],
    ]);

    test('refuses PDF templates that load files or URLs', function (string $content): void {
        app(RenderLabelAction::class)->execute(new LabelableItem(['code' => 'SU-5']), labelingTemplate(LabelFormatEnum::PDF, $content));
    })->throws(LabelRenderException::class)->with([
        'img' => ['<img src="/etc/passwd">'],
        'css url' => ['<div style="background-image: url(file:///etc/passwd)">x</div>'],
        'link' => ['<link rel="stylesheet" href="http://169.254.169.254/">'],
    ]);

    test('qr_svg is a PDF-only placeholder', function (): void {
        app(RenderLabelAction::class)->execute(new LabelableItem(['code' => 'SU-6']), labelingTemplate(LabelFormatEnum::ZPL, '^XA{{ qr_svg }}^XZ'));
    })->throws(LabelRenderException::class);

    test('the seeded default templates are valid and render every line', function (): void {
        $item = new LabelableItem(['code' => 'SU-7', 'name' => 'Big bag']);

        $zpl = app(RenderLabelAction::class)->execute($item, labelingTemplate(LabelFormatEnum::ZPL, LabelTemplateSeeder::ZPL_DEFAULT));

        Assert::assertStringContainsString('^FO300,40^A0N,44,44^FDSU-7^FS', $zpl);
        Assert::assertStringContainsString('^FO300,110^A0N,30,30^FDBig bag^FS', $zpl);
        Assert::assertStringContainsString('^FO300,154^A0N,30,30^FD12.5^FS', $zpl);
        Assert::assertStringNotContainsString('{{', $zpl);
        Assert::assertNull(app(ValidateLabelTemplateContentAction::class)->execute(LabelTemplateSeeder::PDF_DEFAULT, LabelFormatEnum::PDF));
    });
});

describe('ValidateLabelTemplateContentAction', function (): void {
    test('accepts plain text, known placeholders and e-mail addresses', function (): void {
        $error = app(ValidateLabelTemplateContentAction::class)->execute('^XA^FD{{ code }} info@impianto.it {{ data.ewc_code }}^FS^XZ', LabelFormatEnum::ZPL);

        Assert::assertNull($error);
    });

    test('rejects unknown placeholders with a readable message', function (): void {
        $error = app(ValidateLabelTemplateContentAction::class)->execute('^XA{{ secret }}^XZ', LabelFormatEnum::ZPL);

        Assert::assertIsString($error);
        Assert::assertStringContainsString('secret', $error);
    });
});

describe('GenerateQrSvgAction', function (): void {
    test('returns an inline svg of the requested size', function (): void {
        if (! class_exists(QRCode::class)) {
            test()->markTestSkipped('chillerlan/php-qrcode non installato');
        }

        $svg = app(GenerateQrSvgAction::class)->execute('https://host/l/SU-1', 128);

        Assert::assertStringStartsWith('<svg width="128" height="128"', ltrim($svg));
        Assert::assertStringNotContainsString('<?xml', $svg);
    });
});
