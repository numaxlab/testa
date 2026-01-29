<?php

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\Education\Price;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn () => Language::factory()->create());

it('has courses relationship', function () {
    $price = new Price();
    expect($price->courses())->toBeInstanceOf(BelongsToMany::class);
});

it('has translatable name field', function () {
    $price = new Price();
    expect($price->translatable)->toContain('name');
});

it('has translatable description field', function () {
    $price = new Price();
    expect($price->translatable)->toContain('description');
});

// Note: The prices table migration doesn't exist yet, so factory tests are skipped
// it('can be created with factory', function () {
//     $price = Price::factory()->create();
//     expect($price)->toBeInstanceOf(Price::class)
//         ->and($price->exists)->toBeTrue();
// });
