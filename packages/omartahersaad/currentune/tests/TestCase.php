<?php

namespace OmarTaherSaad\CurrenTune\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Concerns\TestDatabases;
use OmarTaherSaad\CurrenTune\CurrenTuneServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    use TestDatabases;

    protected function getPackageProviders($app)
    {
        return [CurrenTuneServiceProvider::class];
    }
}
