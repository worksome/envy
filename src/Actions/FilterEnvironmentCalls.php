<?php

declare(strict_types=1);

namespace Worksome\Envsync\Actions;

use Illuminate\Support\Collection;
use Worksome\Envsync\Contracts\Actions\FiltersEnvironmentCalls;
use Worksome\Envsync\Contracts\Actions\ReadsEnvironmentFile;
use Worksome\Envsync\Support\EnvironmentCall;
use Worksome\Envsync\Support\EnvironmentVariable;

final class FilterEnvironmentCalls implements FiltersEnvironmentCalls
{
    public function __construct(
        private ReadsEnvironmentFile $readEnvironmentFile,
    ) {
    }

    public function __invoke(string $filePath, Collection $environmentCalls): Collection
    {
        $existingKeys = ($this->readEnvironmentFile)($filePath)->map(fn (EnvironmentVariable $variable) => $variable->getKey());

        return $environmentCalls
            ->unique(fn (EnvironmentCall $call) => $call->getKey())
            ->reject(fn (EnvironmentCall $call) => $existingKeys->contains($call->getKey()));
    }
}
