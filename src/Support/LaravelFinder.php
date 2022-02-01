<?php

declare(strict_types=1);

namespace Worksome\Envy\Support;

use Illuminate\Support\LazyCollection;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Worksome\Envy\Contracts\Finder;

final class LaravelFinder implements Finder
{
    /**
     * @param array<int, string> $configFiles
     * @param array<int, string> $environmentFiles
     */
    public function __construct(
        private array $configFiles,
        private array $environmentFiles,
    ) {
    }

    public function configFilePaths(): array
    {
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
        return LazyCollection::make(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)))
            ->reject(fn($file) => $file->isDir())
            ->map(fn($file) => $file->getPathname())
            ->values()
            ->all();
    }

    public function environmentFilePaths(): array
    {
        return $this->environmentFiles;
    }

    public function envyConfigFile(): string|null
    {
        if (! file_exists(config_path('envy.php'))) {
            return null;
        }

        return config_path('envy.php');
    }
}
