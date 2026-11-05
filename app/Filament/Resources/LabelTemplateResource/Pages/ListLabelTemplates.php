<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\LabelTemplateResource\Pages;

use Modules\Labeling\Filament\Resources\LabelTemplateResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseListRecords;

/**
 * Colonne, filtri e azioni stanno in `LabelTemplatesTable`.
 */
class ListLabelTemplates extends XotBaseListRecords
{
    protected static string $resource = LabelTemplateResource::class;
}
