<?php

use Illuminate\Support\Collection;
use Worksome\Envsync\Actions\FilterEnvironmentCalls;
use Worksome\Envsync\Actions\ReadEnvironmentFile;
use Worksome\Envsync\Support\EnvironmentCall;

it('removes duplicates', function () {
    $calls = Collection::times(5, fn () => new EnvironmentCall(
        testAppPath('config/app.php'),
        1,
        'FOO',
        'BAR'
    ));

    $action = new FilterEnvironmentCalls(new ReadEnvironmentFile());

    expect($action(testAppPath('.env.example'), $calls))->toHaveCount(1);
});

it('removes keys that already exist in the .env file', function () {
    $calls = Collection::times(1, fn () => new EnvironmentCall(
        testAppPath('config/app.php'),
        1,
        'APP_URL', // This already exists
    ));

    $action = new FilterEnvironmentCalls(new ReadEnvironmentFile());

    expect($action(testAppPath('.env.example'), $calls))->toHaveCount(0);
});
