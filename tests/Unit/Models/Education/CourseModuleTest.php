<?php

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\TaxClass;
use Testa\Models\Education\CourseModule;
use Testa\Models\EventDeliveryMethod;
use Testa\Observers\CourseObserver;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create();
    TaxClass::factory()->create(['default' => true]);
    Currency::factory()->create(['default' => true]);

    // Create the ProductOption needed by CourseObserver (since Course is created by factory)
    $productOption = ProductOption::factory()->create([
        'handle' => CourseObserver::RATE_PRODUCT_OPTION_HANDLE,
    ]);
    ProductOptionValue::factory()->create([
        'product_option_id' => $productOption->id,
    ]);
});

it('has course relationship', function () {
    $module = new CourseModule();
    expect($module->course())->toBeInstanceOf(BelongsTo::class);
});

it('has venue relationship', function () {
    $module = new CourseModule();
    expect($module->venue())->toBeInstanceOf(BelongsTo::class);
});

it('has instructors relationship', function () {
    $module = new CourseModule();
    expect($module->instructors())->toBeInstanceOf(BelongsToMany::class);
});

it('has products relationship', function () {
    $module = new CourseModule();
    expect($module->products())->toBeInstanceOf(BelongsToMany::class);
});

it('has attachments relationship', function () {
    $module = new CourseModule();
    expect($module->attachments())->toBeInstanceOf(MorphMany::class);
});

it('casts starts_at to datetime', function () {
    $module = CourseModule::factory()->create();
    expect($module->starts_at)->toBeInstanceOf(Carbon::class);
});

it('casts delivery_method to EventDeliveryMethod enum', function () {
    $module = CourseModule::factory()->create();
    expect($module->delivery_method)->toBeInstanceOf(EventDeliveryMethod::class);
});

it('has translatable name field', function () {
    $module = new CourseModule();
    expect($module->translatable)->toContain('name');
});

it('has translatable subtitle field', function () {
    $module = new CourseModule();
    expect($module->translatable)->toContain('subtitle');
});

it('has translatable description field', function () {
    $module = new CourseModule();
    expect($module->translatable)->toContain('description');
});

it('has translatable alert field', function () {
    $module = new CourseModule();
    expect($module->translatable)->toContain('alert');
});

it('can be created with factory', function () {
    $module = CourseModule::factory()->create();
    expect($module)->toBeInstanceOf(CourseModule::class)
        ->and($module->exists)->toBeTrue();
});

it('can create in-person module with factory', function () {
    $module = CourseModule::factory()->inPerson()->create();
    expect($module->delivery_method)->toBe(EventDeliveryMethod::IN_PERSON);
});

it('can create online module with factory', function () {
    $module = CourseModule::factory()->online()->create();
    expect($module->delivery_method)->toBe(EventDeliveryMethod::ONLINE);
});

it('can create hybrid module with factory', function () {
    $module = CourseModule::factory()->hybrid()->create();
    expect($module->delivery_method)->toBe(EventDeliveryMethod::HYBRID);
});
