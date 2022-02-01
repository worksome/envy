<?php

declare(strict_types=1);

namespace Worksome\Envy;

use Illuminate\Contracts\Foundation\Application;
use PhpParser\ParserFactory;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Worksome\Envy\Actions\FilterEnvironmentCalls;
use Worksome\Envy\Actions\FindEnvironmentCalls;
use Worksome\Envy\Actions\FormatEnvironmentCall;
use Worksome\Envy\Actions\ReadEnvironmentFile;
use Worksome\Envy\Actions\UpdateBlacklist;
use Worksome\Envy\Actions\UpdateEnvironmentFile;
use Worksome\Envy\Commands\Sync;
use Worksome\Envy\Contracts\Actions\FiltersEnvironmentCalls;
use Worksome\Envy\Contracts\Actions\FindsEnvironmentCalls;
use Worksome\Envy\Contracts\Actions\FormatsEnvironmentCall;
use Worksome\Envy\Contracts\Actions\ReadsEnvironmentFile;
use Worksome\Envy\Contracts\Actions\UpdatesBlacklist;
use Worksome\Envy\Contracts\Actions\UpdatesEnvironmentFile;
use Worksome\Envy\Contracts\Finder;
use Worksome\Envy\Support\LaravelFinder;

final class EnvyServiceProvider extends PackageServiceProvider
{
    public function packageRegistered(): void
    {
        $this->app->bind(Finder::class, fn() => new LaravelFinder(
            $this->config()['config_files'] ?? [],
            $this->config()['environment_files'] ?? [],
        ));

        $this->app->bind(FindsEnvironmentCalls::class, fn(Application $app) => $app->make(FindEnvironmentCalls::class, [
            'parser' => (new ParserFactory())->create(ParserFactory::PREFER_PHP7),
        ]));
        $this->app->bind(ReadsEnvironmentFile::class, ReadEnvironmentFile::class);
        $this->app->bind(FiltersEnvironmentCalls::class, fn (Application $app) => $app->make(FilterEnvironmentCalls::class, [
            'blacklist' => $this->config()['blacklist'] ?? []
        ]));
        $this->app->bind(UpdatesEnvironmentFile::class, UpdateEnvironmentFile::class);
        $this->app->bind(FormatsEnvironmentCall::class, fn() => new FormatEnvironmentCall(
            $this->config()['display_comments'] ?? false,
            $this->config()['display_location_hints'] ?? false,
            $this->config()['display_default_values'] ?? true,
        ));
        $this->app->bind(UpdatesBlacklist::class, fn (Application $app) => $app->make(UpdateBlacklist::class, [
            'parser' => (new ParserFactory())->create(ParserFactory::PREFER_PHP7),
        ]));
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
