<?php

use Livewire\Livewire;
use Testa\Tests\TestCase;

pest()
    ->extend(TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

beforeEach(function () {
    DB::connection()->disableQueryLog();
});

afterEach(function () {
    session()->flush();
    Livewire::flushState();
    gc_collect_cycles();
});