<?php

declare(strict_types=1);

namespace Worksome\Envy\Support;

final class EnvironmentVariable
{
    public function __construct(
        private string $key,
        private string $value,
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
