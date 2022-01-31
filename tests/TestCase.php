<?php

namespace Worksome\Envsync\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Worksome\Envsync\Contracts\Finder;
use Worksome\Envsync\EnvsyncServiceProvider;
use Worksome\Envsync\Tests\Doubles\TestFinder;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(Finder::class, TestFinder::class);
    }

    protected function getPackageProviders($app)
    {
        return [
            EnvsyncServiceProvider::class,
        ];
    }

    public function defaultPhpParser(): Parser
    {
        return (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
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
