<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\LabelTemplateResource\Pages;

use Modules\Labeling\Filament\Resources\LabelTemplateResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseViewRecord;

class ViewLabelTemplate extends XotBaseViewRecord
{
    protected static string $resource = LabelTemplateResource::class;
}
