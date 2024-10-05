<?php

namespace Chapa\Chapa\Tests;

use Chapa\Chapa\ChapaServiceProvider;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as TestbenchTestCase;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends TestbenchTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ChapaServiceProvider::class,
        ];
    }
}
