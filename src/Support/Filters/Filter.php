<?php

declare(strict_types=1);

namespace Worksome\Envy\Support\Filters;

use Illuminate\Support\Traits\Macroable;

final class Filter
{
    use Macroable;

    /**
     * Create a new Wildcard filter. You may pass a different wildcard
     * filter to the default '*' if required in order to avoid issues
     * where your environment variable already contains a '*'.
     *
     * @param non-empty-string $wildcard
     */
    public static function wildcard(string $variable, string $wildcard = '*'): WildcardFilter
    {
        return new WildcardFilter($variable, $wildcard);
    }

    /**
     * Create a new Regex filter. This filter accepts a regular expression
     * that can be used to create more complex expectation for filtering
     * environment variables when syncing and pruning.
     */
    public static function regex(string $pattern): RegexFilter
    {
        return new RegexFilter($pattern);
    }
}
