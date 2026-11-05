<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\LabelPrintResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Modules\Labeling\Enums\LabelPrintStatusEnum;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

/**
 * Solo lettura: lo storico stampe non si modifica (vedi LabelPrintPolicy::update()).
 */
class LabelPrintForm extends XotBaseResourceForm
{
    /**
     * @return array<string, Component>
     */
    public function getFormSchema(): array
    {
        return [
            'status' => Select::make('status')->options(LabelPrintStatusEnum::class)->disabled(),
            'copies' => TextInput::make('copies')->disabled(),
            'error' => Textarea::make('error')->disabled(),
            'rendered' => Textarea::make('rendered')->rows(12)->disabled()->columnSpanFull(),
        ];
    }
}
