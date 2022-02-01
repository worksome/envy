<?php

declare(strict_types=1);

namespace Worksome\Envy;

use PhpParser\ParserFactory;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Worksome\Envy\Actions\FilterEnvironmentCalls;
use Worksome\Envy\Actions\FindEnvironmentCalls;
use Worksome\Envy\Actions\FormatEnvironmentCall;
use Worksome\Envy\Actions\ReadEnvironmentFile;
use Worksome\Envy\Actions\UpdateEnvironmentFile;
use Worksome\Envy\Commands\Sync;
use Worksome\Envy\Contracts\Actions\FiltersEnvironmentCalls;
use Worksome\Envy\Contracts\Actions\FindsEnvironmentCalls;
use Worksome\Envy\Contracts\Actions\FormatsEnvironmentCall;
use Worksome\Envy\Contracts\Actions\ReadsEnvironmentFile;
use Worksome\Envy\Contracts\Actions\UpdatesEnvironmentFile;
use Worksome\Envy\Contracts\Finder;
use Worksome\Envy\Support\LaravelFinder;

final class EnvyServiceProvider extends PackageServiceProvider
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
        return config('envy');
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('envy')
            ->hasConfigFile()
            ->hasCommands(Sync::class);
    }
}
