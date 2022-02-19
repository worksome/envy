<?php

declare(strict_types=1);

namespace Worksome\Envy\Support\Filters;

use Worksome\Envy\Contracts\Filter;

final class WildcardFilter implements Filter
{
    /**
     * @param non-empty-string $wildcard
     */
    public function __construct(private string $comparison, private string $wildcard)
    {
    }

    public function check(string $environmentVariable): bool
    {
        $comparison = collect(explode($this->wildcard, $this->comparison))
            ->map(fn (string $part) => preg_quote($part))
            ->join('\S+');

        return preg_match("/{$comparison}/", $environmentVariable) === 1;
    }
}
