<?php

declare(strict_types=1);

namespace Worksome\Envy\Support\Filters;

use Worksome\Envy\Contracts\Filter;

final class RegexFilter implements Filter
{
    public function __construct(private string $pattern)
    {
    }

    public function check(string $environmentVariable): bool
    {
        return preg_match($this->pattern, $environmentVariable) === 1;
    }
}
