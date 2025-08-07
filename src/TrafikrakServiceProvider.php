<?php

namespace Trafikrak;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;
use Symfony\Component\Finder\Finder;
use Trafikrak\Console\Commands\Install;

class TrafikrakServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'trafikrak');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'trafikrak');

        Route::middleware('web')->group(fn() => $this->loadRoutesFrom(__DIR__ . '/../routes/storefront.php'));

        Blade::componentNamespace('Trafikrak\\Storefront\\Views\\Components', 'trafikrak');
        Blade::anonymousComponentPath(__DIR__ . '/../resources/views/storefront/components', 'trafikrak');

        $namespace = 'Trafikrak\Storefront\Livewire\\';

        $path = __DIR__ . '/Storefront/Livewire';

        foreach ((new Finder())->in($path)->files() as $file) {
            $component = $namespace . str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());

            if (is_subclass_of($component, Component::class)) {
                $alias = str_replace('.-', '.', Str::kebab(str_replace('\\', '.', $component)));
                Livewire::component($alias, $component);
            }
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                Install::class,
            ]);
        }
    }
}
