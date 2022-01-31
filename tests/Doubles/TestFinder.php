<?php

declare(strict_types=1);

namespace Worksome\Envy\Tests\Doubles;

use Illuminate\Support\Str;
use Worksome\Envy\Contracts\Finder;

final class TestFinder implements Finder
{
    public function configFilePaths(): array
    {
        return [
            $this->path(__DIR__ . '/../Application/config/app.php')
        ];
    }

    public function environmentFilePaths(): array
    {
        return [
            $this->path(__DIR__ . '/../Application/.env.example')
        ];
    }

    public function envyConfigFile(): string|null
    {
        $path = $this->path(__DIR__ . '/../Application/config/envy.php');

        return file_exists($path) ? $path : null;
    }

    private function path(string $path): string
    {
        return Str::replace('/', DIRECTORY_SEPARATOR, $path);
    }
}
