<?php

namespace Trafikrak\Admin\Filament\Resources\Sales\CustomerResource;

use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Support\RelationManagers\BaseRelationManager;

class SubscriptionRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'subscriptions';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Subscripciones';
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function getDefaultTable(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('plan.full_name')
                ->label(__('trafikrak::subscription.table.plan.label')),
            Tables\Columns\TextColumn::make('status')
                ->formatStateUsing(fn(string $state,
                ): string
                    => __("trafikrak::subscription.table.status.options.{$state}"))
                ->label(__('trafikrak::subscription.table.status.label')),
            Tables\Columns\TextColumn::make('started_at')
                ->date()
                ->label(__('trafikrak::subscription.table.started_at.label')),
            Tables\Columns\TextColumn::make('expires_at')
                ->date()
                ->label(__('trafikrak::subscription.table.expires_at.label')),
        ]);
    }
}
