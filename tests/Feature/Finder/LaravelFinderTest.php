<?php

use Worksome\Envy\Support\LaravelFinder;

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
