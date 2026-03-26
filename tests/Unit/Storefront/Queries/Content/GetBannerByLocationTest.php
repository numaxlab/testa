<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Testa\Models\Content\Banner;
use Testa\Models\Content\Location;
use Testa\Storefront\Queries\Content\GetBannerByLocation;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('returns a published banner matching the location', function () {
    Banner::factory()->create([
        'locations' => [Location::USER_DASHBOARD_SUBSCRIPTIONS->value],
        'is_published' => true,
    ]);

    $result = new GetBannerByLocation()->execute(Location::USER_DASHBOARD_SUBSCRIPTIONS);

    expect($result)->not->toBeNull();
});

it('returns null when no banner matches the location', function () {
    Banner::factory()->create([
        'locations' => [Location::COURSE->value],
        'is_published' => true,
    ]);

    $result = new GetBannerByLocation()->execute(Location::USER_DASHBOARD_SUBSCRIPTIONS);

    expect($result)->toBeNull();
});

it('excludes unpublished banners', function () {
    Banner::factory()->create([
        'locations' => [Location::USER_DASHBOARD_SUBSCRIPTIONS->value],
        'is_published' => false,
    ]);

    $result = new GetBannerByLocation()->execute(Location::USER_DASHBOARD_SUBSCRIPTIONS);

    expect($result)->toBeNull();
});

it('eager loads media', function () {
    Banner::factory()->create([
        'locations' => [Location::USER_DASHBOARD_SUBSCRIPTIONS->value],
        'is_published' => true,
    ]);

    $result = new GetBannerByLocation()->execute(Location::USER_DASHBOARD_SUBSCRIPTIONS);

    expect($result->relationLoaded('media'))->toBeTrue();
});
