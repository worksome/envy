<?php

declare(strict_types=1);

namespace Worksome\Envy;

use Illuminate\Support\Collection;
use Worksome\Envy\Contracts\Actions\FiltersEnvironmentCalls;
use Worksome\Envy\Contracts\Actions\FindsEnvironmentCalls;
use Worksome\Envy\Contracts\Actions\AddsEnvironmentVariablesToList;
use Worksome\Envy\Contracts\Actions\FindsEnvironmentVariablesToPrune;
use Worksome\Envy\Contracts\Actions\UpdatesEnvironmentFile;
use Worksome\Envy\Contracts\Finder;
use Worksome\Envy\Support\EnvironmentCall;
use Worksome\Envy\Support\EnvironmentVariable;

final class Envy
{
    public function __construct(
        private AddsEnvironmentVariablesToList $addEnvironmentVariablesToList,
        private FiltersEnvironmentCalls $filtersEnvironmentCalls,
        private Finder $finder,
        private FindsEnvironmentCalls $findEnvironmentCalls,
        private FindsEnvironmentVariablesToPrune $findEnvironmentVariablesToPrune,
        private UpdatesEnvironmentFile $updateEnvironmentFile,
    ) {
    }

    /**
     * Retrieve all calls to `env` found in the configured files.
     *
     * @return Collection<int, EnvironmentCall>
     */
    public function environmentCalls(bool $excludeCallsWithDefaults = false): Collection
    {
        // @phpstan-ignore-next-line
        return collect($this->finder->configFilePaths())
            ->map(fn (string $path) => ($this->findEnvironmentCalls)($path, $excludeCallsWithDefaults))
            ->flatten()
            ->sortBy(fn (EnvironmentCall $call) => $call->getKey());
    }

    /**
     * Map the given environment calls to each configured `.env` file.
     *
     * @see Envy::environmentCalls()
     *
     * @param Collection<int, EnvironmentCall> $environmentCalls
     * @return Collection<string, Collection<int, EnvironmentCall>>
     */
    public function pendingUpdates(Collection $environmentCalls): Collection
    {
        // @phpstan-ignore-next-line
        return collect($this->finder->environmentFilePaths())
            ->flip()
            // @phpstan-ignore-next-line
            ->map(fn (int $index, string $path) => ($this->filtersEnvironmentCalls)($path, $environmentCalls))
            ->filter(fn (Collection $environmentCalls) => $environmentCalls->isNotEmpty());
    }

    /**
     * Perform the given updates on the relevant environment files.
     *
     * @see Envy::pendingUpdates()
     *
     * @param Collection<string, Collection<int, EnvironmentCall>> $pendingUpdates
     */
    public function updateEnvironmentFiles(Collection $pendingUpdates): void
    {
        $pendingUpdates->each(fn (Collection $environmentCalls, string $path) => ($this->updateEnvironmentFile)(
            $path,
            $environmentCalls
        ));
    }

    /**
     * Convert a collection of pending updates to a collection of
     * environment variables and update the config blacklist.
     *
     * @param Collection<string, Collection<int, EnvironmentCall>> $pendingUpdates
     * @throws Exceptions\ConfigFileNotFoundException
     */
    public function updateBlacklistWithPendingUpdates(Collection $pendingUpdates): void
    {
        /** @var Collection<int, EnvironmentVariable> $updates */
        $updates = $pendingUpdates
            ->flatten()
            ->unique(fn (EnvironmentCall $environmentCall) => $environmentCall->getKey())
            // @phpstan-ignore-next-line
            ->map(fn (EnvironmentCall $environmentCall) => new EnvironmentVariable(
                $environmentCall->getKey(),
                $environmentCall->getDefault() ?? ''
            ));

        ($this->addEnvironmentVariablesToList)($updates, AddsEnvironmentVariablesToList::BLACKLIST);
    }

    /**
     * Determine if the envy.php config file has been published to the project.
     */
    public function hasPublishedConfigFile(): bool
    {
        return $this->finder->envyConfigFile() !== null;
    }

    /**
     * Map the environment variables to be removed to each configured .env file.
     *
     * @see Envy::environmentCalls()
     *
     * @param Collection<int, EnvironmentCall> $environmentCalls
     * @param array<int, string>|null $environmentFilePaths
     * @return Collection<string, Collection<int, string>>
     */
    public function pendingPrunes(Collection $environmentCalls, array|null $environmentFilePaths = null): Collection
    {
        // @phpstan-ignore-next-line
        return collect($environmentFilePaths ?? $this->finder->environmentFilePaths())
            ->flip()
            // @phpstan-ignore-next-line
            ->map(fn (int $index, string $path) => ($this->findEnvironmentVariablesToPrune)($path, $environmentCalls));
    }

    public function pruneEnvironmentFiles(): void
    {
    }
}
