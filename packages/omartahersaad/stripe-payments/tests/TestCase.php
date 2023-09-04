<?php

namespace OmarTaherSaad\StripePayments\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Concerns\TestDatabases;
use OmarTaherSaad\StripePayments\StripePaymentServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase  extends BaseTestCase
{
    use RefreshDatabase, TestDatabases;

    protected function getPackageProviders($app)
    {
        return [StripePaymentServiceProvider::class];
    }
}
