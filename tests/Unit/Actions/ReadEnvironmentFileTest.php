<?php

use Worksome\Envy\Actions\ReadEnvironmentFile;
use Worksome\Envy\Exceptions\EnvironmentFileNotFoundException;
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

it('throws an EnvironmentFileNotFoundException if the requested .env file could not be located', function () {
    $action = new ReadEnvironmentFile();
    $action(__DIR__ . '/../../Application/.env.testing');
})->throws(EnvironmentFileNotFoundException::class);

it('does not throw an exception when encountering a variable without a value', function () {
    $action = new ReadEnvironmentFile();

    expect(fn () => $action(__DIR__ . '/../../Application/environments/.env.with-undefined-value'))
        ->not()
        ->toThrow(RuntimeException::class);
});

it('ignores environment variables that have no value', function () {
    $action = new ReadEnvironmentFile();
    $entries = $action(__DIR__ . '/../../Application/environments/.env.with-undefined-value');

    $keys = $entries
        ->map(fn (EnvironmentVariable $variable) => $variable->getKey())
        ->all();

    expect($keys)->not()->toContain('WITHOUT_VALUE');
});
