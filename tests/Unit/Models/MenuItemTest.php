<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\Content\Page;
use Testa\Models\MenuItem;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn() => Language::factory()->create());

it('has translatable name attribute', function () {
    $menuItem = MenuItem::factory()->create([
        'name' => ['en' => 'English Name', 'es' => 'Nombre en Español'],
    ]);

    app()->setLocale('en');
    expect($menuItem->name)->toBe('English Name');

    app()->setLocale('es');
    expect($menuItem->name)->toBe('Nombre en Español');
});

it('has children relationship ordered by sort_position', function () {
    $parent = MenuItem::factory()->create();

    $child3 = MenuItem::factory()->withParent($parent)->create(['sort_position' => 3]);
    $child1 = MenuItem::factory()->withParent($parent)->create(['sort_position' => 1]);
    $child2 = MenuItem::factory()->withParent($parent)->create(['sort_position' => 2]);

    $children = $parent->children;

    expect($children)
        ->toHaveCount(3)
        ->and($children[0]->id)->toBe($child1->id)
        ->and($children[1]->id)->toBe($child2->id)
        ->and($children[2]->id)->toBe($child3->id);
});

it('has parent relationship', function () {
    $parent = MenuItem::factory()->create();
    $child = MenuItem::factory()->withParent($parent)->create();

    expect($child->parent->id)->toBe($parent->id);
});

it('has linkable morph relationship', function () {
    $page = Page::factory()->create();
    $menuItem = MenuItem::factory()->model()->create([
        'linkable_type' => Page::class,
        'linkable_id' => $page->id,
    ]);

    expect($menuItem->linkable)
        ->toBeInstanceOf(Page::class)
        ->and($menuItem->linkable->id)->toBe($page->id);
});

it('returns link_value for manual type url', function () {
    $url = 'https://example.com/custom-page';
    $menuItem = MenuItem::factory()->manual($url)->create();

    expect($menuItem->url)->toBe($url);
});

it('returns route url for route type', function () {
    $menuItem = MenuItem::factory()->route('testa.storefront.homepage')->create();

    expect($menuItem->url)->toBe(route('testa.storefront.homepage'));
});

it('returns linkable url for model type', function () {
    $page = Page::factory()->create();
    $menuItem = MenuItem::factory()->model()->create([
        'linkable_type' => Page::class,
        'linkable_id' => $page->id,
    ]);

    expect($menuItem->url)->toBe($page->url);
});

it('returns hash for model type without linkable', function () {
    $menuItem = MenuItem::factory()->model()->create([
        'linkable_type' => null,
        'linkable_id' => null,
    ]);

    expect($menuItem->url)->toBe('#');
});

it('returns hash for unknown type', function () {
    $menuItem = MenuItem::factory()->create(['type' => 'unknown']);

    expect($menuItem->url)->toBe('#');
});

it('returns breadcrumbs without parent', function () {
    $menuItem = MenuItem::factory()->create(['name' => 'Menu Item']);

    expect($menuItem->breadcrumbs)->toBe('Menu Item');
});

it('returns breadcrumbs with parent', function () {
    $parent = MenuItem::factory()->create(['name' => 'Parent']);
    $child = MenuItem::factory()->withParent($parent)->create(['name' => 'Child']);

    expect($child->breadcrumbs)->toBe('Parent / Child');
});
