<?php

namespace Testa\Admin\Filament\Resources\Membership;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Admin\Support\Resources\BaseResource;
use Testa\Models\Membership\MembershipTier;

class MembershipTierResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = MembershipTier::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function getNavigationGroup(): ?string
    {
        return __('testa::global.sections.membership');
    }

    public static function getLabel(): string
    {
        return __('testa::membership-tier.label');
    }

    public static function getPluralLabel(): string
    {
        return __('testa::membership-tier.plural_label');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-list-bullet';
    }

    public static function getDefaultTable(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('testa::membership-tier.table.name.label')),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('testa::membership-tier.table.is_published.label')),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_position')
            ->defaultSort('sort_position')
            ->searchable();
    }

    public static function getDefaultForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('testa::membership-tier.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Forms\Components\RichEditor::make('description')
                            ->label(__('testa::membership-tier.form.description.label')),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('testa::membership-tier.form.is_published.label')),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => MembershipTierResource\Pages\ListMembershipTiers::route('/'),
            'create' => MembershipTierResource\Pages\CreateMembershipTier::route('/create'),
            'edit' => MembershipTierResource\Pages\EditMembershipTier::route('/{record}/edit'),
        ];
    }
}
