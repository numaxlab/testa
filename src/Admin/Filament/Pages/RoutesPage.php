<?php

namespace Testa\Admin\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Route;

class RoutesPage extends Page
{
    private const ROUTE_MODELS = [
        'testa.storefront.authors.show' => 'Autor/a',
        'testa.storefront.bookshop.sections.show' => 'Colección (sección)',
        'testa.storefront.bookshop.topics.show' => 'Collección (materia)',
        'testa.storefront.bookshop.itineraries.show' => 'Collección (itinerario)',
        'testa.storefront.bookshop.products.show' => 'Producto',
        'testa.storefront.bookshop.page' => 'Página',
        'testa.storefront.editorial.authors.show' => 'Autor/a',
        'testa.storefront.editorial.collections.show' => 'Colección',
        'testa.storefront.editorial.special-collections.show' => 'Colección (especial)',
        'testa.storefront.editorial.page' => 'Página',
        'testa.storefront.education.topics.show' => 'Tema',
        'testa.storefront.education.courses.show' => 'Curso',
        'testa.storefront.education.courses.register' => 'Curso',
        'testa.storefront.education.courses.modules.show' => 'Sesión',
        'testa.storefront.education.page' => 'Página',
        'testa.storefront.media.videos.show' => 'Vídeo',
        'testa.storefront.media.audios.show' => 'Audio',
        'testa.storefront.media.documents.download' => 'Documento',
        'testa.storefront.actualidad.events.show' => 'Evento',
        'testa.storefront.actualidad.articles.show' => 'Noticia',
        'testa.storefront.info.page' => 'Página',
    ];
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static string $view = 'testa::filament.pages.routes-page';

    public static function getNavigationLabel(): string
    {
        return __('Mapa de rutas');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('lunarpanel::global.sections.settings');
    }

    public function getTitle(): string|Htmlable
    {
        return __('Mapa de rutas');
    }

    public function getRouteGroups(): array
    {
        $routes = collect(Route::getRoutes())
            ->filter(fn($route) => str_starts_with($route->getName() ?? '', 'testa.storefront.')
                && $route->getName() !== 'testa.storefront.kitchen-sink')
            ->map(fn($route) => [
                'name' => $route->getName(),
                'uri' => '/' . ltrim($route->uri(), '/'),
                'url' => rtrim(config('app.url'), '/') . '/' . ltrim($route->uri(), '/'),
                'methods' => array_filter($route->methods(), fn($m) => $m !== 'HEAD'),
                'model' => self::ROUTE_MODELS[$route->getName()] ?? null,
            ])
            ->sortBy('name')
            ->values();

        $groups = [];

        foreach ($routes as $route) {
            $parts = explode('.', $route['name']);
            $section = $parts[2] ?? 'general';
            $groups[$section][] = $route;
        }

        ksort($groups);

        return $groups;
    }
}
