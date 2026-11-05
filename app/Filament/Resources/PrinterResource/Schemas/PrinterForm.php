<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\PrinterResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Modules\Labeling\Enums\PrinterDriverEnum;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

/**
 * Lang: Modules/Labeling/lang/{locale}/printer_form.php.
 */
class PrinterForm extends XotBaseResourceForm
{
    public function getFormColumns(): int
    {
        return 2;
    }

    /**
     * @return array<string, Component>
     */
    public function getFormSchema(): array
    {
        return [
            'code' => TextInput::make('code')
                ->required()
                ->maxLength(64)
                ->unique(ignoreRecord: true),
            'name' => TextInput::make('name')
                ->required()
                ->maxLength(255),
            'driver' => Select::make('driver')
                ->options(PrinterDriverEnum::class)
                ->required()
                ->live(),
            'dpi' => Select::make('dpi')
                ->options([152 => '152', 203 => '203', 300 => '300', 600 => '600'])
                ->default(203)
                ->required(),
            'host' => TextInput::make('host')
                ->maxLength(255)
                ->visible(static fn (Get $get): bool => self::driver($get)?->requiresHost() ?? false)
                ->required(static fn (Get $get): bool => self::driver($get)?->requiresHost() ?? false),
            'port' => TextInput::make('port')
                ->integer()
                ->minValue(1)
                ->maxValue(65535)
                ->default(9100)
                ->visible(static fn (Get $get): bool => self::driver($get)?->requiresHost() ?? false),
            'is_active' => Toggle::make('is_active')
                ->default(true),
        ];
    }

    private static function driver(Get $get): ?PrinterDriverEnum
    {
        $state = $get('driver');

        return $state instanceof PrinterDriverEnum
            ? $state
            : PrinterDriverEnum::tryFrom(is_string($state) ? $state : '');
    }
}
