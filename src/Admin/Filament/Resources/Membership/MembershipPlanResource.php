<?php

namespace Trafikrak\Admin\Filament\Resources\Membership;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Admin\Support\Resources\BaseResource;
use Trafikrak\Models\Membership\MembershipPlan;
use Trafikrak\Models\Membership\MembershipTier;

class MembershipPlanResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = MembershipPlan::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function getNavigationGroup(): ?string
    {
        return __('trafikrak::global.sections.membership');
    }

    public static function getLabel(): string
    {
        return __('trafikrak::membership-plan.label');
    }

    public static function getPluralLabel(): string
    {
        return __('trafikrak::membership-plan.plural_label');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-currency-euro';
    }

    public static function getDefaultSubNavigation(): array
    {
        return [
            MembershipPlanResource\Pages\EditMembershipPlan::class,
            MembershipPlanResource\Pages\ManageMembershipPlanPricing::class,
        ];
    }


    public static function getDefaultTable(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tier.name')
                    ->label(__('trafikrak::membership-plan.table.tier_name.label')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('trafikrak::membership-plan.table.name.label')),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('trafikrak::membership-plan.table.is_published.label')),
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
            ->searchable();
    }

    public static function getDefaultForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('membership_tier_id')
                            ->options(MembershipTier::all()->pluck('name', 'id'))
                            ->required()
                            ->label(__('trafikrak::membership-plan.form.tier_id.label')),
                        Forms\Components\TextInput::make('name')
                            ->label(__('trafikrak::membership-plan.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Forms\Components\RichEditor::make('description')
                            ->label(__('trafikrak::membership-plan.form.description.label')),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('trafikrak::membership-plan.form.is_published.label')),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => MembershipPlanResource\Pages\ListMembershipPlans::route('/'),
            'create' => MembershipPlanResource\Pages\CreateMembershipPlan::route('/create'),
            'edit' => MembershipPlanResource\Pages\EditMembershipPlan::route('/{record}/edit'),
            'pricing' => MembershipPlanResource\Pages\ManageMembershipPlanPricing::route('/{record}/pricing'),
        ];
    }
}
