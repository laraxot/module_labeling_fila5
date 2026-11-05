<?php

declare(strict_types=1);

use chillerlan\QRCode\QRCode;
use Modules\Labeling\Actions\GenerateQrSvgAction;
use Modules\Labeling\Actions\RenderLabelAction;
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

describe('RenderLabelAction', function (): void {
    test('renders ZPL without HTML escaping and neutralizes ZPL command characters', function (): void {
        $item = new LabelableItem(['code' => 'SU-1', 'name' => 'Ferro & acciaio ^FS~JA']);
        $template = labelingTemplate(LabelFormatEnum::ZPL, '^XA^PW{{ $widthDots }}^FD{{ $code }}^FS^FD{{ $data[\'name\'] }}^FS^FD{{ $url }}^FS^XZ');

        $zpl = app(RenderLabelAction::class)->execute($item, $template);

        Assert::assertStringContainsString('^PW799^', $zpl);
        Assert::assertStringContainsString('^FDSU-1^FS', $zpl);
        Assert::assertStringContainsString('^FDFerro & acciaio  FS JA^FS', $zpl);
        Assert::assertStringContainsString('/l/SU-1^FS', $zpl);
    });

    test('renders PDF HTML with escaping and the inline QR svg', function (): void {
        if (! class_exists(QRCode::class)) {
            test()->markTestSkipped('chillerlan/php-qrcode non installato');
        }

        $item = new LabelableItem(['code' => 'SU-2', 'name' => '<b>x</b>']);
        $template = labelingTemplate(LabelFormatEnum::PDF, '<div>{{ $code }} {{ $data[\'name\'] }}</div>{!! $qrSvg !!}');

        $html = app(RenderLabelAction::class)->execute($item, $template);

        Assert::assertStringContainsString('<div>SU-2 &lt;b&gt;x&lt;/b&gt;</div>', $html);
        Assert::assertStringContainsString('<svg width="256" height="256"', $html);
    });

    test('wraps template errors in a domain exception', function (): void {
        $item = new LabelableItem(['code' => 'SU-3']);
        $template = labelingTemplate(LabelFormatEnum::ZPL, '^XA{{ $missing->value }}^XZ');

        app(RenderLabelAction::class)->execute($item, $template);
    })->throws(LabelRenderException::class);
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
