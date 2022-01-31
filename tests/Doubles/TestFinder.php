<?php

declare(strict_types=1);

namespace Worksome\Envsync\Tests\Doubles;

use Illuminate\Support\Str;
use Worksome\Envsync\Contracts\Finder;

final class TestFinder implements Finder
{
    public function configDirectory(): string
    {
        return $this->path($this->envExampleDirectory() . '/config');
    }

    public function envExampleDirectory(): string
    {
        return $this->path(__DIR__ . '/../Application');
    }

    private function path(string $path): string
    {
        return Str::replace('/', DIRECTORY_SEPARATOR, $path);
    }
}
