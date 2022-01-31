<?php

declare(strict_types=1);

namespace Worksome\Envsync;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Worksome\Envsync\Actions\FindEnvironmentVariables;
use Worksome\Envsync\Actions\ReadEnvironmentFile;
use Worksome\Envsync\Commands\Sync;
use Worksome\Envsync\Contracts\Actions\FindsEnvironmentVariables;
use Worksome\Envsync\Contracts\Actions\ReadsEnvironmentFile;
use Worksome\Envsync\Contracts\Finder;
use Worksome\Envsync\Support\LaravelFinder;
use Worksome\Envsync\Tests\Doubles\TestFinder;

final class EnvsyncServiceProvider extends PackageServiceProvider
{
    public function packageRegistered(): void
    {
        $this->app->bind(Finder::class, LaravelFinder::class);

        $this->app->bind(FindsEnvironmentVariables::class, FindEnvironmentVariables::class);
        $this->app->bind(ReadsEnvironmentFile::class, ReadEnvironmentFile::class);
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
