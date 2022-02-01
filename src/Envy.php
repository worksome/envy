<?php

declare(strict_types=1);

namespace Worksome\Envy;

use Illuminate\Support\Collection;
use Worksome\Envy\Contracts\Actions\FindsEnvironmentCalls;
use Worksome\Envy\Contracts\Finder;
use Worksome\Envy\Support\EnvironmentCall;

final class Envy
{
    public function __construct(
        private Finder $finder,
        private FindsEnvironmentCalls $findEnvironmentCalls,
    ) {
    }

    /**
     * Retrieve all calls to `env` found in the configured files.
     *
     * @return Collection<int, EnvironmentCall>
     */
    public function environmentCalls(): Collection
    {
        // @phpstan-ignore-next-line
        return collect($this->finder->configFilePaths())
            ->map(fn (string $path) => ($this->findEnvironmentCalls)($path))
            ->flatten();
    }
}
