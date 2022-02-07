<?php

use Worksome\Envy\Actions\FindEnvironmentVariablesToPrune;
use Worksome\Envy\Actions\ReadEnvironmentFile;
use Worksome\Envy\Support\EnvironmentCall;

it('returns the diff of the environment file and the given environment calls', function () {
    $action = new FindEnvironmentVariablesToPrune(new ReadEnvironmentFile());
    $variables = $action(testAppPath('.env.example'), collect([
        new EnvironmentCall(testAppPath('config/app.php'), 1, 'APP_NAME'),
        new EnvironmentCall(testAppPath('config/app.php'), 1, 'APP_TITLE'),
        new EnvironmentCall(testAppPath('config/app.php'), 1, 'APP_DESCRIPTION'),
    ]))->all();

    expect($variables)->toBe([
        'APP_DEBUG',
        'APP_ENV',
        'APP_URL',
        'MIX_URL',
    ]);
});

it('removes duplicates', function () {
    $action = new FindEnvironmentVariablesToPrune(new ReadEnvironmentFile());
    $variables = $action(testAppPath('environments/.env.with-duplicates'), collect())->all();

    expect($variables)->toBe([
        'APP_NAME',
    ]);
});

it('will not include variables on the given inclusions', function () {
    $action = new FindEnvironmentVariablesToPrune(new ReadEnvironmentFile(), [
        'APP_NAME',
        'APP_TITLE'
    ]);
    $variables = $action(testAppPath('.env.example'), collect())->all();

    expect($variables)
        ->not->toContain('APP_NAME')
        ->not->toContain('APP_TITLE');
});
