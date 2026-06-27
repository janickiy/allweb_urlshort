<?php

namespace Tests;

use Database\Seeders\InitialDataSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Feature and service tests expect the app to have completed initial install data.
     */
    protected $seed = true;

    protected $seeder = InitialDataSeeder::class;
}
