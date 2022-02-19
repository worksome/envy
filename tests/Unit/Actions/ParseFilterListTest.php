<?php

use Worksome\Envy\Actions\ParseFilterList;
use Worksome\Envy\Support\Filters\EqualityFilter;

it('transforms strings to EqualityFilters', function () {
    $parseFilterList = new ParseFilterList();

    $result = $parseFilterList([
        'FOO',
        'BAR',
        'BAZ',
        new EqualityFilter('BOOM'),
    ]);

    expect($result)
        ->toHaveCount(4)
        ->each->toBeInstanceOf(EqualityFilter::class);
});
