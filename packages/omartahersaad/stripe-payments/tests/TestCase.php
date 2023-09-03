<?php

namespace OmarTaherSaad\StripePayments\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase  extends BaseTestCase
{
    use CreatesApplication, InteractsWithDatabase, DatabaseTransactions;
}
