<?php

namespace Trafikrak\Tests;

use Livewire\LivewireServiceProvider;
use NumaxLab\Atomic\Laravel\Providers\AtomicServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Trafikrak\TrafikrakServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            AtomicServiceProvider::class,
            TrafikrakServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:H/aZrI0bQaH8dO/i5a2f2gE2xSVI7xKxH5fG4iGg7xM=');

        $app['config']->set('view.paths', [
            __DIR__.'/views',
        ]);
    }
}
