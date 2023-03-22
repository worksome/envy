<?php

namespace Worksome\Envy\Tests\Feature;

use Orchestra\Testbench\TestCase as Orchestra;
use Symfony\Component\Console\Output\BufferedOutput;
use Worksome\Envy\Contracts\Finder;
use Worksome\Envy\EnvyServiceProvider;
use Worksome\Envy\Tests\Concerns\ResetsTestFiles;
use Worksome\Envy\Tests\Doubles\TestFinder;

use function Termwind\renderUsing;

class TestCase extends Orchestra
{
    use ResetsTestFiles;

    protected function setUp(): void
    {
        parent::setUp();
        renderUsing(new BufferedOutput());
        $this->setUpResetsTestFiles();

        if (! in_array('useRealFinder', $this->groups())) {
            $this->app->bind(Finder::class, TestFinder::class);
        }
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
