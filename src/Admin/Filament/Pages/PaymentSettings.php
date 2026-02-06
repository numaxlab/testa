<?php

namespace Testa\Admin\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Contracts\Support\Htmlable;

class PaymentSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $settings = \Testa\Settings\PaymentSettings::class;

    public static function getNavigationLabel(): string
    {
        return __('Métodos de pago');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('lunarpanel::global.sections.settings');
    }

    public function getTitle(): string|Htmlable
    {
        return __('Métodos de pago');
    }

    public function form(Form $form): Form
    {
        $options = \Testa\Settings\PaymentSettings::getAvailablePaymentTypes();

        return $form
            ->schema([
                Forms\Components\Section::make(__('Flujos de pago'))
                    ->description(__('Configura que métodos de pago están disponibles para cada flujo de pago.'))
                    ->schema([
                        Forms\Components\CheckboxList::make('store')
                            ->label(__('Tienda'))
                            ->options($options)
                            ->columns(2),
                        Forms\Components\CheckboxList::make('education')
                            ->label(__('Formación'))
                            ->options($options)
                            ->columns(2),
                        Forms\Components\CheckboxList::make('membership')
                            ->label(__('Membresía'))
                            ->options($options)
                            ->columns(2),
                        Forms\Components\CheckboxList::make('donation')
                            ->label(__('Donación'))
                            ->options($options)
                            ->columns(2),
                    ])->columns(2),
            ]);
    }
}
