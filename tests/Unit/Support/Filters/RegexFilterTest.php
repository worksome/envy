<?php

declare(strict_types=1);

use Worksome\Envy\Support\Filters\RegexFilter;

it('matches based on the given regular expression', function (string $regex, string $comparison, bool $matches) {
    $filter = new RegexFilter($regex);

    expect($filter->check($comparison))->toBe($matches);
})->with([
    ['/\w+/', 'blahblahblah', true],
    ['/\w/', ' ', false],
    ['/\s/', 'FOO_BAR', false],
    ['/\w+_\w+_\w+/', 'FOO_BAR_BAZ', true],
]);
