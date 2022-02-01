<?php

declare(strict_types=1);

namespace Worksome\Envy\Contracts\Actions;

use Illuminate\Support\Collection;
use Worksome\Envy\Exceptions\ConfigFileNotFoundException;
use Worksome\Envy\Support\EnvironmentVariable;

interface UpdatesBlacklist
{
    /**
     * Add all given environment variables to the blacklist config.
     *
     * @param Collection<int, EnvironmentVariable> $updates
     * @throws ConfigFileNotFoundException Thrown when the envy config file hasn't been published.
     */
    public function __invoke(Collection $updates): void;
}
