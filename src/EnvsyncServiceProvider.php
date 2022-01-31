<?php

declare(strict_types=1);

namespace Worksome\Envsync;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Worksome\Envsync\Commands\Sync;
use Worksome\Envsync\Contracts\Finder;
use Worksome\Envsync\Support\LaravelFinder;
use Worksome\Envsync\Tests\Doubles\TestFinder;

final class EnvsyncServiceProvider extends PackageServiceProvider
{
    public function packageRegistered(): void
    {
        $this->app->bind(Finder::class, $this->app->runningUnitTests()
            ? TestFinder::class
            : LaravelFinder::class);
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('envsync')
            ->hasConfigFile()
            ->hasCommands(
                Sync::class,
            );
    }
}
