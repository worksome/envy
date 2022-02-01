<?php

declare(strict_types=1);

namespace Worksome\Envy\Contracts;

interface Finder
{
    /**
     * An array of config file paths to search for `env` calls in.
     *
     * @return array<int, string>
     */
    public function configFilePaths(): array;

    /**
     * An array of environment file paths to be synced.
     *
     * @return array<int, string>
     */
    public function environmentFilePaths(): array;

    /**
     * The config file path for this package. If the config file
     * has not been published, this will return null instead.
     */
    public function envyConfigFile(): string|null;
}
