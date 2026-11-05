<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\IdentifierResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Modules\Labeling\Enums\IdentifierTypeEnum;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

/**
 * Solo lettura: la Resource non ha pagine create/edit, il form serve alla risoluzione Xot
 * (`XotBaseResource::form()`) e alle eventuali viste modali.
 */
class IdentifierForm extends XotBaseResourceForm
{
    /**
     * @return array<string, Component>
     */
    public function getFormSchema(): array
    {
        return [
            'type' => Select::make('type')->options(IdentifierTypeEnum::class)->disabled(),
            'value' => TextInput::make('value')->disabled(),
            'identifiable_type' => TextInput::make('identifiable_type')->disabled(),
            'identifiable_id' => TextInput::make('identifiable_id')->disabled(),
        ];
    }
}
