<?php

use Worksome\Envsync\Actions\FindEnvironmentVariables;
use Worksome\Envsync\Support\EnvironmentCall;

it('can return a collection of environment variables', function (bool $excludeVariablesWithDefaults, int $expectedCount) {
    $action = new FindEnvironmentVariables($this->defaultPhpParser());
    $variables = $action(__DIR__ . '/../../Application/config/app.php', $excludeVariablesWithDefaults);

    expect($variables)
        ->toBeCollection()
        ->toHaveCount($expectedCount)
        ->each->toBeInstanceOf(EnvironmentCall::class);
})->with([
    'variables including defaults' => [false, 7],
    'variables excluding defaults' => [true, 2],
]);

it('contains the correct keys for each environment variable', function () {
    $action = new FindEnvironmentVariables($this->defaultPhpParser());
    $variableNames = $action(__DIR__ . '/../../Application/config/app.php')
        ->map(fn (EnvironmentCall $variable) => $variable->getKey())
        ->all();

    expect($variableNames)->toEqual([
        'APP_NAME',
        'APP_TITLE',
        'APP_NAME',
        'APP_DESCRIPTION',
        'APP_ENV',
        'APP_DEBUG',
        'APP_URL',
    ]);
});

it('correctly retrieves comments', function () {
    $action = new FindEnvironmentVariables($this->defaultPhpParser());
    $appName = $action(__DIR__ . '/../../Application/config/app.php')->first();

    expect($appName->getComment())->toBe(<<<TXT
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */
    TXT);
});

it('correctly handles env calls used as defaults', function () {
    $action = new FindEnvironmentVariables($this->defaultPhpParser());
    $appTitle = $action(__DIR__ . '/../../Application/config/app.php')->get(1);

    expect($appTitle->getDefault())->toBe('env(\'APP_NAME\')');
});
