<?php

declare(strict_types=1);

namespace Worksome\Envy\Support\Filters;

use Worksome\Envy\Contracts\Filter;

final class EqualityFilter implements Filter
{
    public function __construct(private string $comparison)
    {
    }

    public function check(string $environmentVariable): bool
    {
        return $this->comparison === $environmentVariable;
    }
}
