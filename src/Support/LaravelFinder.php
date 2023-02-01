<?php

declare(strict_types=1);

namespace Worksome\Envy\Support;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\LazyCollection;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Worksome\Envy\Contracts\Finder;

final readonly class LaravelFinder implements Finder
{
    /**
     * @param array<int, string> $configFiles
     * @param array<int, string> $environmentFiles
     */
    public function __construct(
        private Application $app,
        private array $configFiles,
        private array $environmentFiles,
    ) {
    }

    public function configFilePaths(): array
    {
        // @phpstan-ignore-next-line
        return collect($this->configFiles)
            ->map(fn(string $path) => is_file($path) ? [$path] : $this->allFilesRecursively($path))
            ->flatten()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function allFilesRecursively(string $directory): array
    {
        // @phpstan-ignore-next-line
        return LazyCollection::make(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)))
            ->filter(fn(mixed $file) => $file instanceof SplFileInfo)
            ->reject(fn(SplFileInfo $file) => $file->isDir())
            ->map(fn(SplFileInfo $file) => $file->getPathname())
            ->values()
            ->all();
    }

    public function environmentFilePaths(): array
    {
        return $this->environmentFiles;
    }

    public function envyConfigFile(): string|null
    {
        if (! file_exists($this->app->configPath('envy.php'))) {
            return null;
        }

        return $this->app->configPath('envy.php');
    }
}
