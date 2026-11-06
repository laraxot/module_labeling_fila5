<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\LabelTemplateResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Modules\Labeling\Actions\ValidateLabelTemplateContentAction;
use Modules\Labeling\Enums\LabelFormatEnum;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

/**
 * Lang: Modules/Labeling/lang/{locale}/label_template_form.php.
 */
class LabelTemplateForm extends XotBaseResourceForm
{
    /** Risoluzioni delle testine termiche piu' diffuse (8, 12, 24 punti/mm). */
    private const array DPI = [152, 203, 300, 600];

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
            'format' => Select::make('format')
                ->options(LabelFormatEnum::class)
                ->required(),
            'labelable_type' => Select::make('labelable_type')
                ->options(static fn (): array => self::labelableOptions()),
            'width_mm' => TextInput::make('width_mm')
                ->integer()
                ->minValue(5)
                ->maxValue(500)
                ->required(),
            'height_mm' => TextInput::make('height_mm')
                ->integer()
                ->minValue(5)
                ->maxValue(500)
                ->required(),
            'dpi' => Select::make('dpi')
                ->options(array_combine(self::DPI, array_map(strval(...), self::DPI)))
                ->default(203)
                ->required(),
            'is_default' => Toggle::make('is_default'),
            'is_active' => Toggle::make('is_active')
                ->default(true),
            'content' => Textarea::make('content')
                ->required()
                ->rows(20)
                ->extraInputAttributes(['class' => 'font-mono text-sm'])
                ->rule(static fn (Get $get): \Closure => static function (string $attribute, mixed $value, \Closure $fail) use ($get): void {
                    $format = self::format($get);
                    if ($format === null || ! is_string($value)) {
                        return;
                    }

                    $error = app(ValidateLabelTemplateContentAction::class)->execute($value, $format);
                    if ($error !== null) {
                        $fail($error);
                    }
                })
                ->columnSpanFull(),
        ];
    }

    private static function format(Get $get): ?LabelFormatEnum
    {
        $state = $get('format');

        return $state instanceof LabelFormatEnum
            ? $state
            : LabelFormatEnum::tryFrom(is_string($state) ? $state : '');
    }

    /**
     * Classi registrate in `config('labeling.labelables')` dai moduli consumer.
     *
     * @return array<string, string>
     */
    private static function labelableOptions(): array
    {
        $options = [];
        foreach (config()->array('labeling.labelables', []) as $class) {
            if (is_string($class) && class_exists($class)) {
                $options[$class] = class_basename($class);
            }
        }

        return $options;
    }
}
