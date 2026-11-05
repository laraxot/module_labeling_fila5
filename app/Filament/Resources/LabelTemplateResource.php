<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources;

use Filament\Resources\RelationManagers\RelationManager;
use Modules\Labeling\Filament\Resources\LabelTemplateResource\Pages\CreateLabelTemplate;
use Modules\Labeling\Filament\Resources\LabelTemplateResource\Pages\EditLabelTemplate;
use Modules\Labeling\Filament\Resources\LabelTemplateResource\Pages\ListLabelTemplates;
use Modules\Labeling\Filament\Resources\LabelTemplateResource\Pages\ViewLabelTemplate;
use Modules\Labeling\Models\LabelTemplate;
use Modules\Xot\Filament\Resources\XotBaseResource;

class LabelTemplateResource extends XotBaseResource
{
    protected static ?string $model = LabelTemplate::class;

    /**
     * @return array<string, class-string<RelationManager>>
     */
    #[\Override]
    public static function getRelations(): array
    {
        return [];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListLabelTemplates::route('/'),
            'create' => CreateLabelTemplate::route('/create'),
            'view' => ViewLabelTemplate::route('/{record}'),
            'edit' => EditLabelTemplate::route('/{record}/edit'),
        ];
    }
}
