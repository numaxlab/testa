<?php

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Testa\Models\Editorial\Review;
use Testa\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(fn () => Language::factory()->create());

it('has product relationship', function () {
    $review = new Review();
    expect($review->product())->toBeInstanceOf(BelongsTo::class);
});

it('has translatable quote field', function () {
    $review = new Review();
    expect($review->translatable)->toContain('quote');
});

it('can be created with factory', function () {
    $review = Review::factory()->create();
    expect($review)->toBeInstanceOf(Review::class)
        ->and($review->exists)->toBeTrue();
});
