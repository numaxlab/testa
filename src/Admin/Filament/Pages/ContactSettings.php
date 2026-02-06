<?php

namespace Testa\Admin\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Contracts\Support\Htmlable;

class ContactSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static string $settings = \Testa\Settings\ContactSettings::class;

    public static function getNavigationLabel(): string
    {
        return __('Información de contacto');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('lunarpanel::global.sections.settings');
    }

    public function getTitle(): string|Htmlable
    {
        return __('Información de contacto');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Básico'))
                    ->schema([
                        Forms\Components\TextInput::make('email_address')
                            ->label(__('Email'))
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('phone_number')
                            ->label(__('Teléfono'))
                            ->required(),
                        Forms\Components\Repeater::make('address')
                            ->label(__('Dirección'))
                            ->schema([
                                Forms\Components\Checkbox::make('is_primary')
                                    ->label(__('Principal')),
                                Forms\Components\TextInput::make('country_iso2')
                                    ->label(__('Código ISO 2 del país')),
                                Forms\Components\TextInput::make('line_one')
                                    ->label(__('Dirección')),
                                Forms\Components\TextInput::make('city')
                                    ->label(__('Ciudad/Población')),
                                Forms\Components\TextInput::make('postcode')
                                    ->label(__('Código postal')),
                                Forms\Components\TextInput::make('opening_hours')
                                    ->label(__('Horario de apertura')),
                            ])->columnSpan(2)->grid(2),
                    ])->columns(2),
                Forms\Components\Section::make(__('Redes Sociales'))
                    ->schema([
                        Forms\Components\TextInput::make('instagram_url')
                            ->label(__('Instagram'))
                            ->url(),
                        Forms\Components\TextInput::make('facebook_url')
                            ->label(__('Facebook'))
                            ->url(),
                        Forms\Components\TextInput::make('x_url')
                            ->label(__('X'))
                            ->url(),
                        Forms\Components\TextInput::make('bluesky_url')
                            ->label(__('Bluesky'))
                            ->url(),
                        Forms\Components\TextInput::make('mastodon_url')
                            ->label(__('Mastodon'))
                            ->url(),
                        Forms\Components\TextInput::make('youtube_url')
                            ->label(__('Youtube'))
                            ->url(),
                        Forms\Components\TextInput::make('vimeo_url')
                            ->label(__('Vimeo'))
                            ->url(),
                        Forms\Components\TextInput::make('soundcloud_url')
                            ->label(__('Soundcloud'))
                            ->url(),
                        Forms\Components\TextInput::make('telegram_url')
                            ->label(__('Telegram'))
                            ->url(),
                        Forms\Components\TextInput::make('whatsapp_url')
                            ->label(__('Whatsapp'))
                            ->url(),
                    ])->columns(2),
            ]);
    }
}
