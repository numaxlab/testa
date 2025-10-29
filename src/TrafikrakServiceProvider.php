<?php

namespace Trafikrak;

use Filament\Support\Facades\FilamentIcon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;
use Lunar\Admin\Filament\Resources\CustomerResource;
use Lunar\Admin\Support\Facades\LunarPanel;
use Lunar\Facades\ModelManifest;
use Lunar\Models\Order;
use Spatie\StructureDiscoverer\Discover;
use Symfony\Component\Finder\Finder;
use Trafikrak\Admin\Filament\Resources\Extension\CustomerResourceExtension;
use Trafikrak\Console\Commands\Install;
use Trafikrak\Observers\OrderObserver;

class TrafikrakServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'trafikrak');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'trafikrak');

        $this->publishes([
            __DIR__.'/../config/trafikrak.php' => config_path('trafikrak.php'),
            __DIR__.'/../resources/views' => resource_path('views/vendor/trafikrak'),
        ], ['trafikrak']);

        Route::middleware('web')
            ->group(fn() => $this->loadRoutesFrom(__DIR__.'/../routes/storefront.php'));

        Blade::componentNamespace('Trafikrak\\Storefront\\Views\\Components', 'trafikrak');
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/storefront/components', 'trafikrak');

        $namespace = 'Trafikrak\Storefront\Livewire\\';

        $path = __DIR__.'/Storefront/Livewire';

        foreach ((new Finder)->in($path)->files() as $file) {
            $component = $namespace.str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());

            if (is_subclass_of($component, Component::class)) {
                $alias = str_replace('.-', '.', Str::kebab(str_replace('\\', '.', $component)));
                Livewire::component($alias, $component);
            }
        }

        FilamentIcon::register([
            'trafikrak::course' => 'lucide-book-plus',
            'trafikrak::course-module' => 'lucide-calendar-range',
        ]);

        $modelClasses = collect(
            Discover::in(__DIR__.'/Models')
                ->classes()
                ->extending(Model::class)
                ->get(),
        )->mapWithKeys(
            fn($class)
                => [
                Str::snake(str_replace('\\', '_', Str::after($class, 'Trafikrak\\Models\\'))) => $class,
            ],
        );

        Relation::morphMap($modelClasses->toArray());

        Order::observe(OrderObserver::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Install::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/trafikrak.php', 'trafikrak');

        ModelManifest::replace(
            \Lunar\Models\Contracts\Customer::class,
            \Trafikrak\Models\Customer::class,
        );

        LunarPanel::extensions([
            CustomerResource::class => CustomerResourceExtension::class,
        ]);
    }
}
