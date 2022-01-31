<?php

namespace Worksome\Envsync\Tests\Feature;

use Orchestra\Testbench\TestCase as Orchestra;
use Worksome\Envsync\Contracts\Finder;
use Worksome\Envsync\EnvsyncServiceProvider;
use Worksome\Envsync\Tests\Concerns\ResetsTestFiles;
use Worksome\Envsync\Tests\Doubles\TestFinder;

class TestCase extends Orchestra
{
    use ResetsTestFiles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpResetsTestFiles();

        $this->app->bind(Finder::class, TestFinder::class);
    }

    protected function tearDown(): void
    {
        $this->tearDownResetsTestFiles();
        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [
            EnvsyncServiceProvider::class,
        ];
    }
}
