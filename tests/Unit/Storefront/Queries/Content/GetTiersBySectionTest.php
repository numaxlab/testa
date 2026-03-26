<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Testa\Models\Content\Section;
use Testa\Models\Content\Tier;
use Testa\Storefront\Queries\Content\GetTiersBySection;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('returns only published tiers for the given section', function () {
    Tier::factory()->create(['section' => Section::BOOKSHOP->value, 'is_published' => true]);

    $result = new GetTiersBySection()->execute(Section::BOOKSHOP);

    expect($result)->toHaveCount(1);
});

it('excludes unpublished tiers', function () {
    Tier::factory()->create(['section' => Section::BOOKSHOP->value, 'is_published' => false]);

    $result = new GetTiersBySection()->execute(Section::BOOKSHOP);

    expect($result)->toBeEmpty();
});

it('excludes tiers from other sections', function () {
    Tier::factory()->create(['section' => Section::EDITORIAL->value, 'is_published' => true]);

    $result = new GetTiersBySection()->execute(Section::BOOKSHOP);

    expect($result)->toBeEmpty();
});

it('returns tiers ordered by sort_position', function () {
    Tier::factory()->create(['section' => Section::BOOKSHOP->value, 'is_published' => true, 'sort_position' => 3]);
    Tier::factory()->create(['section' => Section::BOOKSHOP->value, 'is_published' => true, 'sort_position' => 1]);
    Tier::factory()->create(['section' => Section::BOOKSHOP->value, 'is_published' => true, 'sort_position' => 2]);

    $result = new GetTiersBySection()->execute(Section::BOOKSHOP);

    expect($result->pluck('sort_position')->all())->toBe([1, 2, 3]);
});
