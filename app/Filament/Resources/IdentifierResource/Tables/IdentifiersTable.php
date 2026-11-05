<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\IdentifierResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Modules\Labeling\Actions\RevokeIdentifierAction;
use Modules\Labeling\Enums\IdentifierTypeEnum;
use Modules\Labeling\Models\Identifier;
use Modules\Xot\Filament\Resources\Tables\XotBaseResourceTable;

/**
 * Lang: Modules/Labeling/lang/{locale}/identifiers.php.
 */
class IdentifiersTable extends XotBaseResourceTable
{
    /**
     * @return array<string, Column>
     */
    public function getTableColumns(): array
    {
        return [
            'type' => TextColumn::make('type')->badge()->sortable(),
            'value' => TextColumn::make('value')->searchable()->copyable()->fontFamily('mono'),
            'identifiable_type' => TextColumn::make('identifiable_type')
                ->formatStateUsing(static fn (string $state): string => class_basename($state))
                ->sortable(),
            'identifiable_id' => TextColumn::make('identifiable_id')->sortable(),
            'is_active' => IconColumn::make('is_active')->boolean(),
            'assigned_at' => TextColumn::make('assigned_at')->dateTime()->sortable(),
            'revoked_at' => TextColumn::make('revoked_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    /**
     * @return array<string, BaseFilter>
     */
    public function getTableFilters(): array
    {
        return [
            'type' => SelectFilter::make('type')->options(IdentifierTypeEnum::class),
            'is_active' => TernaryFilter::make('is_active')->default(true),
        ];
    }

    /**
     * @return array<string, Action|ActionGroup>
     */
    public function getTableActions(): array
    {
        return [
            'view' => ViewAction::make()->iconButton(),
            'revoke' => Action::make('revoke')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->iconButton()
                ->requiresConfirmation()
                ->visible(static fn (Identifier $record): bool => $record->is_active)
                ->action(static fn (Identifier $record): Identifier => app(RevokeIdentifierAction::class)->execute($record)),
        ];
    }

    /**
     * Lo storico non si cancella: niente azioni di gruppo.
     *
     * @return array<string, BulkAction>
     */
    public function getTableBulkActions(): array
    {
        return [];
    }
}
