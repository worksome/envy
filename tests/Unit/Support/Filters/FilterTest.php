<?php

use Worksome\Envy\Support\Filters\Filter;
use Worksome\Envy\Support\Filters\RegexFilter;
use Worksome\Envy\Support\Filters\WildcardFilter;

it('can create a wildcard filter', function () {
    expect(Filter::wildcard('FOO_*'))->toBeInstanceOf(WildcardFilter::class);
});

it('can create a regex filter', function () {
    expect(Filter::regex('/\w+_\w+/'))->toBeInstanceOf(RegexFilter::class);
});
