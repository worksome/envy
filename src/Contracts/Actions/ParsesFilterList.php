<?php

declare(strict_types=1);

namespace Worksome\Envy\Contracts\Actions;

use Worksome\Envy\Contracts\Filter;

interface ParsesFilterList
{
    /**
     * @param array<int, string|Filter> $list
     *
     * @return array<int, Filter>
     */
    public function __invoke(array $list): array;
}
