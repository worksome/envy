<?php

declare(strict_types=1);

use Worksome\Envy\Support\Filters\EqualityFilter;

it('succeeds if the given value is identical', function (mixed $value, mixed $comparison, bool $result) {
    $filter = new EqualityFilter($comparison);

    expect($filter->environmentVariableMatches($value))->toBe($result);
})->with([
    ['foo', 'foo', true],
    ['foo', 'bar', false],
]);
