<?php

namespace Testa\Admin\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Contracts\Support\Htmlable;

class EmailSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static string $settings = \Testa\Settings\EmailSettings::class;

    public static function getNavigationLabel(): string
    {
        return __('Emails');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('lunarpanel::global.sections.settings');
    }

    public function getTitle(): string|Htmlable
    {
        return __('Textos de emails');
    }

    public function form(Form $form): Form
    {
        $locales = Filament::getPlugin('spatie-laravel-translatable')->getDefaultLocales();

        return $form
            ->schema([
                Forms\Components\Section::make(__('Email de confirmación de pedido'))
                    ->schema([
                        Forms\Components\Tabs::make()
                            ->tabs(collect($locales)->map(fn($locale) => Forms\Components\Tabs\Tab::make(strtoupper($locale))
                                ->schema([
                                    Forms\Components\TextInput::make("order_confirmation_greeting.{$locale}")
                                        ->label(__('Asunto/Título'))
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make("order_confirmation_intro.{$locale}")
                                        ->label(__('Introducción'))
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ]),
                            )->toArray(),
                            )->columnSpanFull(),
                    ]),
                Forms\Components\Section::make(__('Email de pedido finalizado pero pendiente de pago'))
                    ->schema([
                        Forms\Components\Tabs::make()
                            ->tabs(collect($locales)->map(fn($locale) => Forms\Components\Tabs\Tab::make(strtoupper($locale))
                                ->schema([
                                    Forms\Components\TextInput::make("order_pending_payment_greeting.{$locale}")
                                        ->label(__('Asunto/Título'))
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make("order_pending_payment_intro.{$locale}")
                                        ->label(__('Introducción'))
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ]),
                            )->toArray(),
                            )->columnSpanFull(),
                    ]),
            ]);
    }
}
