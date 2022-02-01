<?php

declare(strict_types=1);

namespace Worksome\Envsync;

use PhpParser\ParserFactory;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Worksome\Envsync\Actions\FilterEnvironmentCalls;
use Worksome\Envsync\Actions\FindEnvironmentCalls;
use Worksome\Envsync\Actions\FormatEnvironmentCall;
use Worksome\Envsync\Actions\ReadEnvironmentFile;
use Worksome\Envsync\Actions\UpdateEnvironmentFile;
use Worksome\Envsync\Commands\Sync;
use Worksome\Envsync\Contracts\Actions\FiltersEnvironmentCalls;
use Worksome\Envsync\Contracts\Actions\FindsEnvironmentCalls;
use Worksome\Envsync\Contracts\Actions\FormatsEnvironmentCall;
use Worksome\Envsync\Contracts\Actions\ReadsEnvironmentFile;
use Worksome\Envsync\Contracts\Actions\UpdatesEnvironmentFile;
use Worksome\Envsync\Contracts\Finder;
use Worksome\Envsync\Support\LaravelFinder;

final class EnvsyncServiceProvider extends PackageServiceProvider
{
    public function packageRegistered(): void
    {
        $this->app->bind(Finder::class, fn() => new LaravelFinder($this->config()));

        $this->app->bind(FindsEnvironmentCalls::class, fn() => new FindEnvironmentCalls(
            (new ParserFactory())->create(ParserFactory::PREFER_PHP7)
        ));
        $this->app->bind(ReadsEnvironmentFile::class, ReadEnvironmentFile::class);
        $this->app->bind(FiltersEnvironmentCalls::class, FilterEnvironmentCalls::class);
        $this->app->bind(UpdatesEnvironmentFile::class, UpdateEnvironmentFile::class);
        $this->app->bind(FormatsEnvironmentCall::class, fn() => new FormatEnvironmentCall(
            $this->config()['display_comments'],
            $this->config()['display_location_hints'],
            $this->config()['display_default_values'],
        ));
    }

    private function config(): array
    {
        // @phpstan-ignore-next-line
        return config('envsync');
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
