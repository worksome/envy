<?php

declare(strict_types=1);

namespace Worksome\Envsync\Support;

use Worksome\Envsync\Contracts\Finder;

use function base_path;
use function config_path;

final class LaravelFinder implements Finder
{
    public function configDirectory(): string
    {
        return config_path();
    }

    public function envExampleDirectory(): string
    {
        return base_path();
    }
}
