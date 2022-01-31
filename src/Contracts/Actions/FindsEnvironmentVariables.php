<?php

declare(strict_types=1);

namespace Worksome\Envsync\Contracts\Actions;

use Illuminate\Support\Collection;
use Worksome\Envsync\Support\EnvironmentCall;

interface FindsEnvironmentVariables
{
    /**
     * @return Collection<int, EnvironmentCall>
     */
    public function __invoke(string $filePath, bool $excludeVariablesWithDefaults = false): Collection;
}
