<?php

declare(strict_types=1);

namespace Worksome\Envy;

use Illuminate\Contracts\Foundation\Application;
use PhpParser\ParserFactory;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Worksome\Envy\Actions\AddEnvironmentVariablesToList;
use Worksome\Envy\Actions\FilterEnvironmentCalls;
use Worksome\Envy\Actions\FindEnvironmentCalls;
use Worksome\Envy\Actions\FindEnvironmentVariablesToPrune;
use Worksome\Envy\Actions\FormatEnvironmentCall;
use Worksome\Envy\Actions\ParseFilterList;
use Worksome\Envy\Actions\PruneEnvironmentFile;
use Worksome\Envy\Actions\ReadEnvironmentFile;
use Worksome\Envy\Actions\UpdateEnvironmentFile;
use Worksome\Envy\Commands\InstallCommand;
use Worksome\Envy\Commands\PruneCommand;
use Worksome\Envy\Commands\SyncCommand;
use Worksome\Envy\Contracts\Actions\AddsEnvironmentVariablesToList;
use Worksome\Envy\Contracts\Actions\FiltersEnvironmentCalls;
use Worksome\Envy\Contracts\Actions\FindsEnvironmentCalls;
use Worksome\Envy\Contracts\Actions\FindsEnvironmentVariablesToPrune;
use Worksome\Envy\Contracts\Actions\FormatsEnvironmentCall;
use Worksome\Envy\Contracts\Actions\ParsesFilterList;
use Worksome\Envy\Contracts\Actions\PrunesEnvironmentFile;
use Worksome\Envy\Contracts\Actions\ReadsEnvironmentFile;
use Worksome\Envy\Contracts\Actions\UpdatesEnvironmentFile;
use Worksome\Envy\Contracts\Finder;
use Worksome\Envy\Support\LaravelFinder;

final class EnvyServiceProvider extends PackageServiceProvider
{
    public function packageRegistered(): void
    {
        $this->app->bind(Finder::class, fn(Application $app) => new LaravelFinder(
            $app,
            $this->config()['config_files'] ?? [],
            $this->config()['environment_files'] ?? [],
        ));

        $this->app->bind(
            FindsEnvironmentCalls::class,
            fn(Application $app) => $app->make(FindEnvironmentCalls::class, [
                'parser' => (new ParserFactory())->createForNewestSupportedVersion(),
            ])
        );
        $this->app->bind(ReadsEnvironmentFile::class, ReadEnvironmentFile::class);
        $this->app->bind(ParsesFilterList::class, ParseFilterList::class);
        $this->app->bind(
            FiltersEnvironmentCalls::class,
            fn (Application $app) => $app->make(FilterEnvironmentCalls::class, [
                'exclusions' => $this->config()['exclusions'] ?? []
            ])
        );
        $this->app->bind(UpdatesEnvironmentFile::class, UpdateEnvironmentFile::class);
        $this->app->bind(FormatsEnvironmentCall::class, fn() => new FormatEnvironmentCall(
            $this->config()['display_comments'] ?? false,
            $this->config()['display_location_hints'] ?? false,
            $this->config()['display_default_values'] ?? true,
        ));
        $this->app->bind(
            AddsEnvironmentVariablesToList::class,
            fn (Application $app) => $app->make(AddEnvironmentVariablesToList::class, [
                'parser' => (new ParserFactory())->createForNewestSupportedVersion(),
            ])
        );
        $this->app->bind(
            FindsEnvironmentVariablesToPrune::class,
            fn (Application $app) => $app->make(FindEnvironmentVariablesToPrune::class, [
                'inclusions' => $this->config()['inclusions'] ?? [],
            ])
        );
        $this->app->bind(PrunesEnvironmentFile::class, PruneEnvironmentFile::class);
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
            ->hasCommands(
                InstallCommand::class,
                SyncCommand::class,
                PruneCommand::class,
            );
    }
}
