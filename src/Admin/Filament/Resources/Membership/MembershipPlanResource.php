<?php

namespace Testa\Admin\Filament\Resources\Membership;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Admin\Support\Resources\BaseResource;
use Testa\Models\Membership\MembershipPlan;
use Testa\Models\Membership\MembershipTier;
use Testa\Settings\PaymentSettings;

class MembershipPlanResource extends BaseResource
{
    use Translatable;

    protected static ?string $model = MembershipPlan::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function getNavigationGroup(): ?string
    {
        return __('testa::global.sections.membership');
    }

    public static function getLabel(): string
    {
        return __('testa::membership-plan.label');
    }

    public static function getPluralLabel(): string
    {
        return __('testa::membership-plan.plural_label');
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
                    ->label(__('testa::membership-plan.table.tier_name.label')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('testa::membership-plan.table.name.label')),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('testa::membership-plan.table.is_published.label')),
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
                        Forms\Components\Select::make('membership_tier_id')
                            ->options(MembershipTier::all()->pluck('name', 'id'))
                            ->required()
                            ->label(__('testa::membership-plan.form.tier_id.label')),
                        Forms\Components\TextInput::make('name')
                            ->label(__('testa::membership-plan.form.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Forms\Components\RichEditor::make('description')
                            ->label(__('testa::membership-plan.form.description.label')),
                        Forms\Components\Select::make('benefits')
                            ->relationship(titleAttribute: 'name')
                            ->multiple()
                            ->preload()
                            ->label(__('testa::membership-plan.form.benefits.label')),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('testa::membership-plan.form.is_published.label')),
                        Forms\Components\CheckboxList::make('payment_types')
                            ->label(__('testa::membership-plan.form.payment_types.label'))
                            ->options(PaymentSettings::getAvailablePaymentTypes())
                            ->columns(2),
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
