<?php

declare(strict_types=1);

namespace Modules\Labeling\Actions;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Modules\Labeling\Exceptions\LabelRenderException;
use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;

use function Safe\preg_replace;

/**
 * SVG inline del QR (chillerlan/php-qrcode), senza header XML: si incolla nel template PDF come
 * `{{ qr_svg }}`. Correzione d'errore M (15%): regge graffi e sporco tipici di un piazzale.
 * Le opzioni usate esistono sia in php-qrcode 5.x (PHP 8.3) sia in 6.x (PHP 8.4).
 */
class GenerateQrSvgAction
{
    use QueueableAction;

    public function execute(string $payload, int $sizePx = 256): string
    {
        Assert::stringNotEmpty($payload);
        Assert::greaterThan($sizePx, 0);

        try {
            $options = new QROptions([
                'outputInterface' => QRMarkupSVG::class,
                'outputBase64' => false,
                'svgAddXmlHeader' => false,
                'eccLevel' => EccLevel::M,
                'addQuietzone' => true,
            ]);

            $svg = (new QRCode($options))->render($payload);
        } catch (\Throwable $e) {
            throw LabelRenderException::qr($e);
        }

        Assert::string($svg);

        return preg_replace('/<svg\b/', sprintf('<svg width="%1$d" height="%1$d"', $sizePx), $svg, 1) ?? $svg;
    }
}
