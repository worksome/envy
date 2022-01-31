<?php

declare(strict_types=1);

namespace Worksome\Envsync\Contracts\Actions;

use Illuminate\Support\Collection;
use Worksome\Envsync\Support\EnvironmentCall;

interface FiltersEnvironmentCalls
{
    /**
     * @param Collection<int, EnvironmentCall> $environmentCalls
     * @return Collection<int, EnvironmentCall>
     */
    public function __invoke(string $filePath, Collection $environmentCalls): Collection;
}
