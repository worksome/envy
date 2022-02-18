<?php

use Illuminate\Support\Collection;
use Worksome\Envy\Actions\FilterEnvironmentCalls;
use Worksome\Envy\Actions\ParseFilterList;
use Worksome\Envy\Actions\ReadEnvironmentFile;
use Worksome\Envy\Contracts\Filter;
use Worksome\Envy\Support\EnvironmentCall;

it('removes duplicates', function () {
    $calls = Collection::times(5, fn () => new EnvironmentCall(
        testAppPath('config/app.php'),
        1,
        'FOO',
        'BAR'
    ));

    $action = new FilterEnvironmentCalls(new ReadEnvironmentFile(), new ParseFilterList());

    expect($action(testAppPath('.env.example'), $calls))->toHaveCount(1);
});

it('removes keys that already exist in the .env file', function () {
    $calls = Collection::times(1, fn () => new EnvironmentCall(
        testAppPath('config/app.php'),
        1,
        'APP_URL', // This already exists
    ));

    $action = new FilterEnvironmentCalls(new ReadEnvironmentFile(), new ParseFilterList());

    expect($action(testAppPath('.env.example'), $calls))->toHaveCount(0);
});

it('removes keys from the given exclusions', function () {
    $calls = Collection::times(1, fn () => new EnvironmentCall(
        testAppPath('config/app.php'),
        1,
        'FOO_BAR', // This already exists
    ));

    $action = new FilterEnvironmentCalls(
        new ReadEnvironmentFile(),
        new ParseFilterList(),
        ['FOO_BAR']
    );

    expect($action(testAppPath('.env.example'), $calls))->toHaveCount(0);
});

it('can parse Filters in exclusions', function (bool $variableMatches, int $expectedFilteredCallCount) {
    $customFilter = new class ($variableMatches) implements Filter {
        public function __construct(private bool $variableMatches)
        {
        }

        public function environmentVariableMatches(string $environmentVariable): bool
        {
            return $this->variableMatches;
        }
    };

    $calls = Collection::times(1, fn () => new EnvironmentCall(
        testAppPath('config/app.php'),
        1,
        'FOO_BAR',
    ));

    $action = new FilterEnvironmentCalls(
        new ReadEnvironmentFile(),
        new ParseFilterList(),
        [$customFilter],
    );

    expect($action(testAppPath('.env.example'), $calls))->toHaveCount($expectedFilteredCallCount);
})->with([
    [true, 0],
    [false, 1],
]);
