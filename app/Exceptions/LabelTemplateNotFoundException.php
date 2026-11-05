<?php

declare(strict_types=1);

namespace Modules\Labeling\Exceptions;

class LabelTemplateNotFoundException extends LabelingException
{
    public static function forType(string $labelableType): self
    {
        return new self((string) __('labeling::exceptions.template_not_found', [
            'type' => class_basename($labelableType),
        ]));
    }
}
