<?php

use Worksome\Envsync\Support\LaravelFinder;

it('can return all configured config files', function () {
    $finder = new LaravelFinder(
        ['config_files' => [
            testAppPath('config'),
            __DIR__ . '/../../../config/envsync.php',
        ]]
    );

    expect($finder->configFilePaths())->toBe([
        testAppPath('config/app.php'),
        testAppPath('config/nested/config.php'),
        __DIR__ . '/../../../config/envsync.php',
    ]);
});
