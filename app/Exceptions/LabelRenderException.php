<?php

declare(strict_types=1);

namespace Modules\Labeling\Exceptions;

class LabelRenderException extends LabelingException
{
    public static function template(string $templateCode, \Throwable $previous): self
    {
        return new self((string) __('labeling::exceptions.render_failed', [
            'template' => $templateCode,
            'reason' => $previous->getMessage(),
        ]), 0, $previous);
    }

    public static function qr(\Throwable $previous): self
    {
        return new self((string) __('labeling::exceptions.qr_failed', [
            'reason' => $previous->getMessage(),
        ]), 0, $previous);
    }

    public static function pdf(\Throwable $previous): self
    {
        return new self((string) __('labeling::exceptions.pdf_failed', [
            'reason' => $previous->getMessage(),
        ]), 0, $previous);
    }
}
