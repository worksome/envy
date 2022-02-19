<?php

declare(strict_types=1);

use Worksome\Envy\Support\Filters\WildcardFilter;

it('matches based on the given wildcard character', function (string $variable, string $comparison, bool $matches) {
    $filter = new WildcardFilter($variable, '%');

    expect($filter->check($comparison))->toBe($matches);
})->with([
    ['FOO_%', 'FOO_BAR', true],
    ['FOO_%', 'FOO_BAR_BAR', true],
    ['FOO_%', 'FOO_BAZ', true],
    ['FOO_%', 'FOO_', false],
    ['FOO_%', 'BAZ_FOO', false],
    ['FOO_%', 'BAZ', false],
    ['FOO%BAR', 'FOO_BAR', true],
    ['%BAR', 'FOOBAR', true],
]);
