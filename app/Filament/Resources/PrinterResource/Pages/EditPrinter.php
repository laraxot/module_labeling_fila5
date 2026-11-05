<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\PrinterResource\Pages;

use Modules\Labeling\Filament\Resources\PrinterResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseEditRecord;

class EditPrinter extends XotBaseEditRecord
{
    protected static string $resource = PrinterResource::class;
}
