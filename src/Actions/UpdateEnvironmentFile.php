<?php

declare(strict_types=1);

namespace Worksome\Envsync\Actions;

use Illuminate\Support\Collection;
use Worksome\Envsync\Contracts\Actions\FormatsEnvironmentCall;
use Worksome\Envsync\Contracts\Actions\ReadsEnvironmentFile;
use Worksome\Envsync\Contracts\Actions\UpdatesEnvironmentFile;
use Worksome\Envsync\Support\EnvironmentCall;
use Worksome\Envsync\Support\EnvironmentVariable;

final class UpdateEnvironmentFile implements UpdatesEnvironmentFile
{
    public function __construct(
        private ReadsEnvironmentFile $readEnvironmentFile,
        private FormatsEnvironmentCall $formatEnvironmentCall,
    ) {
    }

    public function __invoke(string $filePath, Collection $environmentCalls): void
    {
        $content = $this->variablesToAdd($filePath, $environmentCalls)
            ->map(fn (EnvironmentCall $call) => ($this->formatEnvironmentCall)($call))
            ->join(PHP_EOL);

        file_put_contents($filePath, $content, FILE_APPEND);
    }

    /**
     * Removes any environment calls that are duplicates or
     * already exist in the given env file.
     *
     * @param Collection<int, EnvironmentCall> $environmentCalls
     * @return Collection<int, EnvironmentCall>
     */
    private function variablesToAdd(string $filePath, Collection $environmentCalls): Collection
    {
        $existingKeys = ($this->readEnvironmentFile)($filePath)->map(fn (EnvironmentVariable $variable) => $variable->getKey());

        return $environmentCalls
            ->unique(fn (EnvironmentCall $call) => $call->getKey())
            ->reject(fn (EnvironmentCall $call) => $existingKeys->contains($call->getKey()));
    }
}
