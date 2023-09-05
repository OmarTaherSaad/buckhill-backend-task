<?php

namespace OmarTaherSaad\CurrenTune\Tests;

use Illuminate\Testing\Concerns\TestDatabases;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OmarTaherSaad\CurrenTune\CurrenTuneServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    use TestDatabases;

    protected function getPackageProviders($app)
    {
        return [CurrenTuneServiceProvider::class];
    }
}
