<?php

declare(strict_types=1);

namespace Worksome\Envy\Contracts\Actions;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Worksome\Envy\Exceptions\ConfigFileNotFoundException;
use Worksome\Envy\Support\EnvironmentVariable;

interface AddsEnvironmentVariablesToList
{
    public const EXCLUSIONS = 'exclusions';
    public const INCLUSIONS = 'inclusions';

    /**
     * Add all given environment variables to the given list key in the envy config.
     *
     * @param Collection<int, EnvironmentVariable> $updates
     *
     * @throws ConfigFileNotFoundException Thrown when the envy config file hasn't been published.
     * @throws InvalidArgumentException    Thrown when the envy config file doesn't contain the given key.
     */
    public function __invoke(Collection $updates, string $listKey): void;
}
