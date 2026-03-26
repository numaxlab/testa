<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\TaxClass;
use NumaxLab\Lunar\Geslib\Models\Author;
use Testa\Models\Education\CourseModule;
use Testa\Observers\CourseObserver;
use Testa\Storefront\Queries\Education\CheckAuthorHasMedia;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    $productOption = ProductOption::factory()->create([
        'handle' => CourseObserver::RATE_PRODUCT_OPTION_HANDLE,
    ]);
    ProductOptionValue::factory()->create([
        'product_option_id' => $productOption->id,
    ]);

    $this->author = Author::factory()->create();
});

it('returns true when a published module exists for the author', function () {
    $module = CourseModule::factory()->create(['is_published' => true]);
    $module->instructors()->attach($this->author->id, ['position' => 1]);

    $result = new CheckAuthorHasMedia()->execute($this->author);

    expect($result)->toBeTrue();
});

it('returns false when no modules exist for the author', function () {
    $result = new CheckAuthorHasMedia()->execute($this->author);

    expect($result)->toBeFalse();
});

it('returns false when modules exist but none are published', function () {
    $module = CourseModule::factory()->create(['is_published' => false]);
    $module->instructors()->attach($this->author->id, ['position' => 1]);

    $result = new CheckAuthorHasMedia()->execute($this->author);

    expect($result)->toBeFalse();
});

it('returns false when published modules exist for a different author', function () {
    $otherAuthor = Author::factory()->create();
    $module = CourseModule::factory()->create(['is_published' => true]);
    $module->instructors()->attach($otherAuthor->id, ['position' => 1]);

    $result = new CheckAuthorHasMedia()->execute($this->author);

    expect($result)->toBeFalse();
});
