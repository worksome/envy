<?php

use Worksome\Envy\Actions\PruneEnvironmentFile;

it('removes the given entries from the given environment file', function () {
    $action = new PruneEnvironmentFile();
    $action(testAppPath('.env.example'), collect(['MIX_URL']));

    $this->assertFileChanged(testAppPath('.env.example'), function (string $newContent) {
        return $newContent === <<<TXT
        # The Application Name
        APP_NAME=
        APP_ENV=local
        APP_DEBUG=true
        APP_URL=http://laravel.com

        TXT;
    });
});

it('', function () {
    $action = new PruneEnvironmentFile();
    $action(testAppPath('.env.example'), collect(['APP_NAME']));

    $this->assertFileChanged(testAppPath('.env.example'), function (string $newContent) {
        return $newContent === <<<TXT

        APP_ENV=local
        APP_DEBUG=true
        APP_URL=http://laravel.com


        MIX_URL=\${APP_URL}

        TXT;
    });
});

it('removes duplicate entries', function () {
    $this->addResettableFile(testAppPath('environments/.env.with-duplicates'));
    $action = new PruneEnvironmentFile();
    $action(testAppPath('environments/.env.with-duplicates'), collect(['APP_NAME']));

    $this->assertFileChanged(testAppPath('environments/.env.with-duplicates'), function (string $newContent) {
        return $newContent === "\n";
    });
});
