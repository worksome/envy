<?php

declare(strict_types=1);

namespace Worksome\Envy\Contracts;

interface Filter
{
    public function check(string $environmentVariable): bool;
}
