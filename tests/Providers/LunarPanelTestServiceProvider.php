<?php

namespace Testa\Tests\Providers;

use Illuminate\Support\ServiceProvider;
use NumaxLab\Lunar\Geslib\Admin\Filament\GeslibPlugin;
use Testa\Admin\Filament\TestaPlugin;

class LunarPanelTestServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        \Lunar\Admin\Support\Facades\LunarPanel::panel(function ($panel) {
            return $panel->plugins([
                GeslibPlugin::make(),
                TestaPlugin::make(),
            ]);
        })->register();
    }
}
