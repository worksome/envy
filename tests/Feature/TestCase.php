<?php

namespace Worksome\Envy\Tests\Feature;

use Orchestra\Testbench\TestCase as Orchestra;
use Worksome\Envy\Contracts\Finder;
use Worksome\Envy\EnvyServiceProvider;
use Worksome\Envy\Tests\Concerns\ResetsTestFiles;
use Worksome\Envy\Tests\Doubles\TestFinder;

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
            EnvyServiceProvider::class,
        ];
    }

    public function shouldUseAction(string $action, mixed $returnValue = null): self
    {
        $this->mock($action)
            ->shouldReceive('__invoke')
            ->atLeast()->once()
            ->andReturn(value($returnValue));

        return $this;
    }
}
