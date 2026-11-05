<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\LabelTemplateResource\Pages;

use Modules\Labeling\Filament\Resources\LabelTemplateResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseEditRecord;

class EditLabelTemplate extends XotBaseEditRecord
{
    protected static string $resource = LabelTemplateResource::class;
}
