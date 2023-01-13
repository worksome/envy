<?php

declare(strict_types=1);

namespace Worksome\Envy\Actions;

use Illuminate\Support\Collection;
use Worksome\Envy\Contracts\Actions\FindsEnvironmentVariablesToPrune;
use Worksome\Envy\Contracts\Actions\ParsesFilterList;
use Worksome\Envy\Contracts\Actions\ReadsEnvironmentFile;
use Worksome\Envy\Contracts\Filter;
use Worksome\Envy\Support\EnvironmentCall;
use Worksome\Envy\Support\EnvironmentVariable;

final class FindEnvironmentVariablesToPrune implements FindsEnvironmentVariablesToPrune
{
    /**
     * @param array<int, string|Filter> $inclusions
     */
    public function __construct(
        private ReadsEnvironmentFile $readEnvironmentFile,
        private ParsesFilterList $parsesFilterList,
        private array $inclusions = [],
    ) {
    }

    public function __invoke(string $filePath, Collection $environmentCalls): Collection
    {
        $variablesInEnvironmentCalls = $environmentCalls->map(
            fn(EnvironmentCall $environmentCall) => $environmentCall->getKey()
        );

        return $this->environmentVariables($filePath)
            ->diff($variablesInEnvironmentCalls)
            ->reject(fn (string $variable) => $this->inclusionsContainVariable($variable))
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * @return Collection<int, string>
     */
    private function environmentVariables(string $filePath): Collection
    {
        return ($this->readEnvironmentFile)($filePath)
            ->map(fn(EnvironmentVariable $environmentVariable) => $environmentVariable->getKey());
    }

    private function inclusionsContainVariable(string $environmentVariable): bool
    {
        return collect(($this->parsesFilterList)($this->inclusions))
            ->filter(fn (Filter $filter) => $filter->check($environmentVariable))
            ->isNotEmpty();
    }
}
