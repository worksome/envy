<?php

declare(strict_types=1);

namespace Worksome\Envy\Support;

final class EnvironmentCall
{
    /**
     * @param string      $file    The file path containing this environment variable
     * @param int         $line    The line number the environment variable was found on
     * @param string      $key     The key used to define the environment variable
     * @param string|null $default The default passed to the env call, if given
     * @param string|null $comment The PHP comment directly above the env call, if given
     */
    public function __construct(
        private string $file,
        private int $line,
        private string $key,
        private string|null $default = null,
        private string|null $comment = null,
    ) {
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getDefault(): string|null
    {
        return $this->default;
    }

    public function getComment(): string|null
    {
        return $this->comment;
    }
}
