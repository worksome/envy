<?php

declare(strict_types=1);

namespace Worksome\Envsync\Contracts\Actions;

use Illuminate\Support\Collection;
use Worksome\Envsync\Support\EnvironmentVariable;

interface FindsEnvironmentVariables
{
    /**
     * @return Collection<int, EnvironmentVariable>
     */
    public function __invoke(string $filePath, bool $excludeVariablesWithDefaults = false): Collection;
}
