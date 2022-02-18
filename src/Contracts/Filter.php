<?php

declare(strict_types=1);

namespace Worksome\Envy\Contracts;

interface Filter
{
    public function environmentVariableMatches(string $environmentVariable): bool;
}
