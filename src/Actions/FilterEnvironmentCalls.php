<?php

declare(strict_types=1);

namespace Worksome\Envy\Actions;

use Illuminate\Support\Collection;
use Worksome\Envy\Contracts\Actions\FiltersEnvironmentCalls;
use Worksome\Envy\Contracts\Actions\ReadsEnvironmentFile;
use Worksome\Envy\Support\EnvironmentCall;
use Worksome\Envy\Support\EnvironmentVariable;

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
