<?php

namespace Trafikrak\Admin\Filament\Resources\Membership\MembershipPlanResource\Pages;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Support\Pages\BaseEditRecord;
use Lunar\Models\Currency;
use Lunar\Models\Price;
use Lunar\Models\TaxClass;
use Trafikrak\Admin\Filament\Resources\Membership\MembershipPlanResource;
use Trafikrak\Models\Membership\MembershipPlan;

class ManageMembershipPlanPricing extends BaseEditRecord
{
    protected static string $resource = MembershipPlanResource::class;

    public ?string $tax_class_id = '';

    public ?string $billing_interval = '';

    public array $basePrices = [];

    public static function getNavigationIcon(): ?string
    {
        return FilamentIcon::resolve('lunar::product-pricing');
    }

    public static function getNavigationLabel(): string
    {
        return __('lunarpanel::relationmanagers.pricing.title');
    }

    public function getTitle(): string|Htmlable
    {
        return __('lunarpanel::relationmanagers.pricing.title');
    }

    public function form(Form $form): Form
    {
        if (!count($this->basePrices)) {
            $this->basePrices = $this->getBasePrices();
        }

        $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Group::make([
                        Forms\Components\Select::make('tax_class_id')
                            ->label(
                                __('lunarpanel::productvariant.form.tax_class_id.label'),
                            )
                            ->options(TaxClass::all()->pluck('name', 'id'))
                            ->required(),
                        Forms\Components\Select::make('billing_interval')
                            ->label('Periodicidad')
                            ->options([
                                MembershipPlan::BILLING_INTERVAL_MONTHLY => __('Mensual'),
                                MembershipPlan::BILLING_INTERVAL_BIMONTHLY => __('Bimensual'),
                                MembershipPlan::BILLING_INTERVAL_QUARTERLY => __('Trimestral'),
                                MembershipPlan::BILLING_INTERVAL_YEARLY => __('Anual'),
                            ])
                            ->required(),
                    ])->columns(2),
                ]),
            $this->getBasePriceFormSection(),
        ])->statePath('');

        return $form;
    }

    protected function getBasePrices(): array
    {
        $currencies = Currency::whereEnabled(true)
            ->orderBy('default', 'desc')
            ->orderBy('name')
            ->get();

        $prices = collect();

        $basePrices = $this
            ->getRecord()
            ->basePrices()
            ->with('currency')
            ->get()
            ->sortByDesc(fn($p) => (int) $p->currency->default)
            ->values();

        foreach ($basePrices as $price) {
            $prices->put(
                $price->currency->code,
                [
                    'id' => $price->id,
                    'original_value' => $price->price->decimal(rounding: false),
                    'value' => $price->price->decimal(rounding: false),
                    'original_compare_price' => $price->compare_price->decimal(rounding: false),
                    'compare_price' => $price->compare_price->decimal(rounding: false),
                    'factor' => $price->currency->factor,
                    'label' => $price->currency->name,
                    'currency_code' => $price->currency->code,
                    'default_currency' => $price->currency->default,
                    'sync_prices' => $price->currency->sync_prices,
                    'currency_id' => $price->currency_id,
                ],
            );
        }

        $defaultCurrencyPrice = $prices->first(
            fn($price) => $price['default_currency'],
        );

        foreach ($currencies as $currency) {
            if (!$prices->get($currency->code)) {
                $value = round(
                    ($defaultCurrencyPrice['value'] ?? 0) * $currency->exchange_rate,
                    $currency->decimal_places,
                );

                $prices->put($currency->code, [
                    'id' => null,
                    'original_value' => $value,
                    'value' => $value,
                    'compare_price' => round(
                        ($defaultCurrencyPrice['compare_price'] ?? 0) * $currency->exchange_rate,
                        $currency->decimal_places,
                    ),
                    'factor' => $currency->factor,
                    'label' => $currency->name,
                    'currency_code' => $currency->code,
                    'default_currency' => $currency->default,
                    'sync_prices' => $currency->sync_prices,
                    'currency_id' => $currency->id,
                ]);
            }
        }

        return $prices->values()->toArray();
    }

    public function getBasePriceFormSection(): Section
    {
        return Forms\Components\Section::make(
            __('lunarpanel::relationmanagers.pricing.form.basePrices.title'),
        )
            ->schema(
                collect($this->basePrices)->map(callback: function ($price, $index): Forms\Components\Fieldset {
                    return Forms\Components\Fieldset::make($price['label'])->schema([
                        Forms\Components\TextInput::make('value')
                            ->label('')
                            ->statePath($index.'.value')
                            ->numeric()
                            ->label(
                                __('lunarpanel::relationmanagers.pricing.form.basePrices.form.price.label'),
                            )
                            ->helperText(
                                __('lunarpanel::relationmanagers.pricing.form.basePrices.form.price.helper_text'),
                            )
                            ->hintColor('warning')
                            ->extraInputAttributes([
                                'class' => '',
                            ])
                            ->hintIcon(function (Forms\Get $get, Forms\Components\TextInput $component) use (
                                $index,
                                $price,
                            ) {
                                if (!($price['sync_prices'] ?? false) && $get('basePrices.'.$index.'.id', true)) {
                                    return null;
                                }

                                return FilamentIcon::resolve('lunar::info');
                            })->hintIconTooltip(function (Forms\Get $get, Forms\Components\TextInput $component) use (
                                $index,
                                $price,
                            ) {
                                if ($price['sync_prices'] ?? false) {
                                    return __('lunarpanel::relationmanagers.pricing.form.basePrices.form.price.sync_price');
                                }

                                if ($get('basePrices.'.$index.'.id', true)) {
                                    return null;
                                }

                                return __('lunarpanel::relationmanagers.pricing.form.basePrices.tooltip');
                            })
                            ->disabled(fn() => $price['sync_prices'] ?? false)
                            ->live(),
                        Forms\Components\TextInput::make('compare_price')
                            ->label('')
                            ->statePath($index.'.compare_price')
                            ->numeric()
                            ->label(
                                __('lunarpanel::relationmanagers.pricing.form.basePrices.form.compare_price.label'),
                            )
                            ->helperText(
                                __('lunarpanel::relationmanagers.pricing.form.basePrices.form.compare_price.helper_text'),
                            )
                            ->hintColor('warning')
                            ->extraInputAttributes([
                                'class' => '',
                            ])
                            ->hintIcon(function (Forms\Get $get, Forms\Components\TextInput $component) use (
                                $index,
                                $price,
                            ) {
                                if (!($price['sync_prices'] ?? false) && $get('basePrices.'.$index.'.id', true)) {
                                    return null;
                                }

                                return FilamentIcon::resolve('lunar::info');
                            })->hintIconTooltip(function (Forms\Get $get, Forms\Components\TextInput $component) use (
                                $index,
                                $price,
                            ) {
                                if ($price['sync_prices'] ?? false) {
                                    return __('lunarpanel::relationmanagers.pricing.form.basePrices.form.price.sync_price');
                                }

                                if ($get('basePrices.'.$index.'.id', true)) {
                                    return null;
                                }

                                return __('lunarpanel::relationmanagers.pricing.form.basePrices.tooltip');
                            })
                            ->disabled(fn() => $price['sync_prices'] ?? false)
                            ->live(),
                    ])->columns(2);
                })->toArray(),
            )->statePath('basePrices')->columns(1);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data = $this->callLunarHook('beforeUpdate', $data, $record);

        $membershipPlan = $this->getRecord();

        $prices = collect($data['basePrices']);
        unset($data['basePrices']);
        $membershipPlan->update($data);

        $prices->filter(
            fn($price) => !$price['id'] && isset($price['value']),
        )->each(fn($price) => $membershipPlan->prices()->create([
            'currency_id' => $price['currency_id'],
            'price' => (int) round((float) ($price['value'] * $price['factor'])),
            'compare_price' => (int) round((float) ($price['compare_price'] * $price['factor'])),
            'min_quantity' => 1,
            'customer_group_id' => null,
        ]));

        $prices->filter(function ($price) {
            return $price['id'] && isset($price['value']) && ($price['value'] != $price['original_value'] || $price['compare_price'] != $price['original_compare_price']);
        })->each(fn($price) => Price::find($price['id'])->update([
            'price' => (int) round((float) ($price['value'] * $price['factor'])),
            'compare_price' => (int) round((float) ($price['compare_price'] * $price['factor'])),
        ]));

        $this->basePrices = $this->getBasePrices();

        $this->dispatch('refresh-relation-manager');

        return $this->callLunarHook('afterUpdate', $record, $data);
    }
}
