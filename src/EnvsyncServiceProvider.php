<?php

declare(strict_types=1);

namespace Worksome\Envsync;

use Illuminate\Contracts\Foundation\Application;
use PhpParser\ParserFactory;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Worksome\Envsync\Actions\FindEnvironmentVariables;
use Worksome\Envsync\Actions\FormatEnvironmentCall;
use Worksome\Envsync\Actions\ReadEnvironmentFile;
use Worksome\Envsync\Actions\UpdateEnvironmentFile;
use Worksome\Envsync\Commands\Sync;
use Worksome\Envsync\Contracts\Actions\FindsEnvironmentVariables;
use Worksome\Envsync\Contracts\Actions\FormatsEnvironmentCall;
use Worksome\Envsync\Contracts\Actions\ReadsEnvironmentFile;
use Worksome\Envsync\Contracts\Actions\UpdatesEnvironmentFile;
use Worksome\Envsync\Contracts\Finder;
use Worksome\Envsync\Support\LaravelFinder;

final class EnvsyncServiceProvider extends PackageServiceProvider
{
    public function packageRegistered(): void
    {
        $this->app->bind(Finder::class, LaravelFinder::class);

        $this->app->bind(FindsEnvironmentVariables::class, fn() => new FindEnvironmentVariables(
            (new ParserFactory())->create(ParserFactory::PREFER_PHP7)
        ));
        $this->app->bind(ReadsEnvironmentFile::class, ReadEnvironmentFile::class);
        $this->app->bind(UpdatesEnvironmentFile::class, UpdateEnvironmentFile::class);
        $this->app->bind(FormatsEnvironmentCall::class, function (Application $app) {
            // @phpstan-ignore-next-line
            $config = $app->get('config')->get('envsync');

            return new FormatEnvironmentCall(
                $config['display_comments'],
                $config['display_location_hints'],
                $config['display_default_values'],
            );
        });
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
