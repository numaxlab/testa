<?php

use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;
use Lunar\Facades\CartSession;
use Lunar\Facades\StorefrontSession;
use Testa\Tests\TestCase;

pest()
    ->extend(TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

beforeEach(function () {
    DB::connection()->disableQueryLog();
});

afterEach(function () {
    // Clear session data
    session()->flush();

    // Clear Livewire state
    Livewire::flushState();

    // Clear Lunar facades state
    CartSession::forget();
    StorefrontSession::forget();

    // Clear Eloquent's resolved instance caches
    Model::clearBootedModels();

    // Force garbage collection
    gc_collect_cycles();
});