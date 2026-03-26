<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\TaxClass;
use NumaxLab\Lunar\Geslib\Models\Author;
use Testa\Models\Education\CourseModule;
use Testa\Observers\CourseObserver;
use Testa\Storefront\Queries\Editorial\GetAuthorCourseModules;
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

it('returns a collection', function () {
    $result = new GetAuthorCourseModules()->execute($this->author);

    expect($result)->toBeInstanceOf(Collection::class);
});

it('returns published modules for the author', function () {
    $module = CourseModule::factory()->create(['is_published' => true]);
    $module->instructors()->attach($this->author->id, ['position' => 1]);

    $result = new GetAuthorCourseModules()->execute($this->author);

    expect($result)
        ->toHaveCount(1)
        ->and($result->first()->id)->toBe($module->id);
});

it('excludes unpublished modules', function () {
    $module = CourseModule::factory()->create(['is_published' => false]);
    $module->instructors()->attach($this->author->id, ['position' => 1]);

    $result = new GetAuthorCourseModules()->execute($this->author);

    expect($result)->toBeEmpty();
});

it('does not return modules belonging to other authors', function () {
    $otherAuthor = Author::factory()->create();
    $module = CourseModule::factory()->create(['is_published' => true]);
    $module->instructors()->attach($otherAuthor->id, ['position' => 1]);

    $result = new GetAuthorCourseModules()->execute($this->author);

    expect($result)->toBeEmpty();
});
