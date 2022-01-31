<?php

declare(strict_types=1);

namespace Worksome\Envy\Contracts\Actions;

use Illuminate\Support\Collection;
use Worksome\Envy\Support\EnvironmentCall;

interface FindsEnvironmentCalls
{
    /**
     * @return Collection<int, EnvironmentCall>
     */
    public function __invoke(string $filePath, bool $excludeVariablesWithDefaults = false): Collection;
}
