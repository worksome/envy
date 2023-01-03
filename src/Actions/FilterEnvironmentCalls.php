<?php

declare(strict_types=1);

namespace Worksome\Envy\Actions;

use Illuminate\Support\Collection;
use Worksome\Envy\Contracts\Actions\FiltersEnvironmentCalls;
use Worksome\Envy\Contracts\Actions\ParsesFilterList;
use Worksome\Envy\Contracts\Actions\ReadsEnvironmentFile;
use Worksome\Envy\Contracts\Filter;
use Worksome\Envy\Support\EnvironmentCall;
use Worksome\Envy\Support\EnvironmentVariable;

final class FilterEnvironmentCalls implements FiltersEnvironmentCalls
{
    /**
     * @param array<int, string|Filter> $exclusions
     */
    public function __construct(
        private ReadsEnvironmentFile $readEnvironmentFile,
        private ParsesFilterList $parseFilterList,
        private array $exclusions = [],
    ) {
    }

    public function __invoke(string $filePath, Collection $environmentCalls): Collection
    {
        $existingKeys = ($this->readEnvironmentFile)($filePath)->map(
            fn(EnvironmentVariable $variable) => $variable->getKey()
        );

        return $environmentCalls
            ->unique(fn(EnvironmentCall $call) => $call->getKey())
            ->reject(fn(EnvironmentCall $call) => $existingKeys->contains($call->getKey()))
            ->reject(fn(EnvironmentCall $call) => $this->exclusionsContainVariable($call->getKey()));
    }

    private function exclusionsContainVariable(string $environmentVariable): bool
    {
        return collect(($this->parseFilterList)($this->exclusions))
            ->filter(fn (Filter $filter) => $filter->check($environmentVariable))
            ->isNotEmpty();
    }
}
