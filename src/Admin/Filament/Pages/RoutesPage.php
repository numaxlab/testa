<?php

namespace Testa\Admin\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Route;

class RoutesPage extends Page
{
    private const ROUTE_MODELS = [
        'testa.storefront.authors.show' => 'Author',
        'testa.storefront.bookshop.sections.show' => 'Collection (sección)',
        'testa.storefront.bookshop.topics.show' => 'Collection (materia)',
        'testa.storefront.bookshop.itineraries.show' => 'Collection (itinerario)',
        'testa.storefront.bookshop.products.show' => 'Product',
        'testa.storefront.bookshop.page' => 'Page',
        'testa.storefront.editorial.authors.show' => 'Author',
        'testa.storefront.editorial.collections.show' => 'Collection',
        'testa.storefront.editorial.special-collections.show' => 'Collection (especial)',
        'testa.storefront.editorial.page' => 'Page',
        'testa.storefront.education.topics.show' => 'Topic',
        'testa.storefront.education.courses.show' => 'Course',
        'testa.storefront.education.courses.register' => 'Course',
        'testa.storefront.education.courses.modules.show' => 'CourseModule',
        'testa.storefront.education.page' => 'Page',
        'testa.storefront.media.videos.show' => 'Video',
        'testa.storefront.media.audios.show' => 'Audio',
        'testa.storefront.media.documents.download' => 'Document',
        'testa.storefront.actualidad.events.show' => 'Event',
        'testa.storefront.actualidad.articles.show' => 'Article',
        'testa.storefront.info.page' => 'Page',
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
