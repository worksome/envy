<?php

use Worksome\Envy\Actions\ReadEnvironmentFile;
use Worksome\Envy\Support\EnvironmentVariable;

it('returns a collection of environment variables', function () {
    $action = new ReadEnvironmentFile();
    $entries = $action(__DIR__ . '/../../Application/.env.example');

    expect($entries)->toHaveCount(5);
});

it('reads the keys correctly', function () {
    $action = new ReadEnvironmentFile();
    $keys = $action(__DIR__ . '/../../Application/.env.example')
        ->map(fn (EnvironmentVariable $variable) => $variable->getKey())
        ->all();

    expect($keys)->toEqual([
        'APP_NAME',
        'APP_ENV',
        'APP_DEBUG',
        'APP_URL',
        'MIX_URL',
    ]);
});
