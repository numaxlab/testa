<?php

namespace Testa;

use Filament\Support\Facades\FilamentIcon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\Mechanisms\ComponentRegistry;
use Lunar\Admin\Filament\Resources\CustomerResource;
use Lunar\Admin\Filament\Resources\ProductResource;
use Lunar\Admin\Support\Facades\LunarPanel;
use Lunar\Facades\ModelManifest;
use Lunar\Models\Order;
use Spatie\StructureDiscoverer\Discover;
use Symfony\Component\Finder\Finder;
use Testa\Admin\Filament\Extension\ProductResourceExtension;
use Testa\Admin\Filament\Resources\Extension\CustomerResourceExtension;
use Testa\Admin\Filament\Support\RelationManagers\CourseMediaRelationManager;
use Testa\Console\Commands\Install;
use Testa\Models\Education\Course;
use Testa\Models\Membership\MembershipPlan;
use Testa\Models\Membership\MembershipTier;
use Testa\Observers\CourseObserver;
use Testa\Observers\MembershipPlanObserver;
use Testa\Observers\MembershipTierObserver;
use Testa\Observers\OrderObserver;

class TestaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'testa');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'testa');

        $this->publishes([
            __DIR__.'/../config/testa.php' => config_path('testa.php'),
            __DIR__.'/../resources/views' => resource_path('views/vendor/testa'),
        ], ['testa']);

        Route::middleware('web')
            ->group(fn() => $this->loadRoutesFrom(__DIR__.'/../routes/storefront.php'));

        Blade::componentNamespace('Testa\\Storefront\\Views\\Components', 'testa');
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components', 'testa');

        View::prependNamespace('numaxlab-atomic', __DIR__.'/../resources/views/vendor/numaxlab-atomic');

        $namespace = 'Testa\Storefront\Livewire\\';

        $path = __DIR__.'/Storefront/Livewire';

        foreach ((new Finder)->in($path)->files() as $file) {
            $component = $namespace.str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());

            if (is_subclass_of($component, Component::class)) {
                $alias = str_replace('.-', '.', Str::kebab(str_replace('\\', '.', $component)));
                Livewire::component($alias, $component);
            }
        }

        FilamentIcon::register([
            'testa::course' => 'lucide-book-plus',
            'testa::course-module' => 'lucide-calendar-range',
        ]);

        $componentName = app(ComponentRegistry::class)->getName(CourseMediaRelationManager::class);
        Livewire::component($componentName, CourseMediaRelationManager::class);

        $modelClasses = collect(
            Discover::in(__DIR__.'/Models')
                ->classes()
                ->extending(Model::class)
                ->get(),
        )->mapWithKeys(
            fn($class)
                => [
                Str::snake(str_replace('\\', '_', Str::after($class, 'Testa\\Models\\'))) => $class,
            ],
        );

        Relation::morphMap($modelClasses->toArray());

        Order::observe(OrderObserver::class);
        Course::observe(CourseObserver::class);
        MembershipTier::observe(MembershipTierObserver::class);
        MembershipPlan::observe(MembershipPlanObserver::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Install::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/testa.php', 'testa');

        ModelManifest::replace(
            \Lunar\Models\Contracts\Product::class,
            \Testa\Models\Product::class,
        );

        ModelManifest::replace(
            \Lunar\Models\Contracts\Customer::class,
            \Testa\Models\Customer::class,
        );

        LunarPanel::extensions([
            CustomerResource::class => CustomerResourceExtension::class,
            ProductResource::class => ProductResourceExtension::class,
        ]);
    }
}
