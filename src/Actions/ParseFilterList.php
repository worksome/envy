<?php

declare(strict_types=1);

namespace Worksome\Envy\Actions;

use Worksome\Envy\Contracts\Actions\ParsesFilterList;
use Worksome\Envy\Contracts\Filter;
use Worksome\Envy\Support\Filters\EqualityFilter;

final class ParseFilterList implements ParsesFilterList
{
    public function __invoke(array $list): array
    {
        return array_map(fn (string|Filter $filter) => $this->transformFilter($filter), $list);
    }

    private function transformFilter(string|Filter $filter): Filter
    {
        if ($filter instanceof Filter) {
            return $filter;
        }

        return new EqualityFilter($filter);
    }
}
