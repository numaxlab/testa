<?php

namespace Testa\Admin\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Contracts\Support\Htmlable;

class TextSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $settings = \Testa\Settings\TextSettings::class;

    public static function getNavigationLabel(): string
    {
        return __('Textos');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('lunarpanel::global.sections.settings');
    }

    public function getTitle(): string|Htmlable
    {
        return __('Textos');
    }

    public function form(Form $form): Form
    {
        $locales = Filament::getPlugin('spatie-laravel-translatable')->getDefaultLocales();

        return $form
            ->schema([
                Forms\Components\Section::make(__('Socias'))
                    ->schema([
                        Forms\Components\Tabs::make()
                            ->tabs(collect($locales)->map(fn($locale)
                                => Forms\Components\Tabs\Tab::make(strtoupper($locale))
                                ->schema([
                                    Forms\Components\RichEditor::make("membership_intro.{$locale}")
                                        ->label(__('Texto introductorio de la página de asociación'))
                                        ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                                        ->columnSpanFull(),
                                    Forms\Components\RichEditor::make("membership_options_description.{$locale}")
                                        ->label(__('Descripción de las opciones de cuota'))
                                        ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                                        ->columnSpanFull(),
                                ]),
                            )->toArray(),
                            )->columnSpanFull(),
                    ]),
                Forms\Components\Section::make(__('Política de privacidad'))
                    ->schema([
                        Forms\Components\Tabs::make()
                            ->tabs(collect($locales)->map(fn($locale)
                                => Forms\Components\Tabs\Tab::make(strtoupper($locale))
                                ->schema([
                                    Forms\Components\RichEditor::make("privacy_policy_text.{$locale}")
                                        ->label(__('Texto descriptivo'))
                                        ->toolbarButtons(['bold', 'italic', 'link'])
                                        ->columnSpanFull(),
                                ]),
                            )->toArray(),
                            )->columnSpanFull(),
                    ]),
            ]);
    }
}
