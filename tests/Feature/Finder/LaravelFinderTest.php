<?php

use Worksome\Envy\Contracts\Finder;
use Worksome\Envy\Support\LaravelFinder;

it('can be resolved correctly', function () {
    expect($this->app->make(Finder::class))->toBeInstanceOf(LaravelFinder::class);
})->group('useRealFinder');

it('can return all configured config files', function () {
    $finder = new LaravelFinder(
        [
            testAppPath('config'),
            __DIR__ . '/../../../config/envy.php',
        ],
        []
    );

    expect($finder->configFilePaths())->toBe([
        testAppPath('config/app.php'),
        testAppPath('config/nested/config.php'),
        __DIR__ . '/../../../config/envy.php',
    ]);
});

it('can return all configured environment files', function () {
    $finder = new LaravelFinder(
        [],
        [
            testAppPath('.env.example'),
            testAppPath('.env'),
        ]
    );

    expect($finder->environmentFilePaths())->toBe([
        testAppPath('.env.example'),
        testAppPath('.env'),
    ]);
});
