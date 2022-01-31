<?php

namespace Worksome\Envsync\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Worksome\Envsync\Contracts\Finder;
use Worksome\Envsync\EnvsyncServiceProvider;
use Worksome\Envsync\Tests\Doubles\TestFinder;
use function Safe\file_get_contents;
use function Safe\file_put_contents;

class TestCase extends Orchestra
{
    private static array $filesToReset = [
        __DIR__ . '/Application/.env.example',
    ];

    private array $fileContents = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(Finder::class, TestFinder::class);

        foreach (self::$filesToReset as $path) {
            $this->fileContents[$path] = file_get_contents($path);
        }
    }

    protected function tearDown(): void
    {
        foreach (self::$filesToReset as $path) {
            file_put_contents($path, $this->fileContents[$path]);
        }

        parent::tearDown();
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
