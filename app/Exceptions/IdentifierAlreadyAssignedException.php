<?php

declare(strict_types=1);

namespace Modules\Labeling\Exceptions;

use Modules\Labeling\Enums\IdentifierTypeEnum;

class IdentifierAlreadyAssignedException extends LabelingException
{
    public static function for(IdentifierTypeEnum $type, string $value, ?\Throwable $previous = null): self
    {
        return new self((string) __('labeling::exceptions.identifier_already_assigned', [
            'type' => $type->getLabel(),
            'value' => $value,
        ]), 0, $previous);
    }
}
