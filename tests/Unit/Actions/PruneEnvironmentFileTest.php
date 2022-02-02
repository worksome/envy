<?php

use Worksome\Envy\Actions\PruneEnvironmentFile;

it('removes the given entries from the given environment file', function () {
    $action = new PruneEnvironmentFile();
    $action(testAppPath('.env.example'), collect(['MIX_URL']));

    $this->assertFileChanged(testAppPath('.env.example'), function (string $newContent) {
        $eol = PHP_EOL;
        return $newContent === "# The Application Name{$eol}APP_NAME={$eol}APP_ENV=local{$eol}APP_DEBUG=true{$eol}APP_URL=http://laravel.com\n";
    });
});

it('will remove comments above entries', function () {
    $action = new PruneEnvironmentFile();
    $action(testAppPath('.env.example'), collect(['APP_NAME']));

    $this->assertFileChanged(testAppPath('.env.example'), function (string $newContent) {
        dump($newContent);
        $eol = PHP_EOL;
        return $newContent === dump("{$eol}APP_ENV=local{$eol}APP_DEBUG=true{$eol}APP_URL=http://laravel.com{$eol}{$eol}{$eol}MIX_URL=\${APP_URL}{$eol}");
    });
});

it('removes duplicate entries', function () {
    $this->addResettableFile(testAppPath('environments/.env.with-duplicates'));
    $action = new PruneEnvironmentFile();
    $action(testAppPath('environments/.env.with-duplicates'), collect(['APP_NAME']));

    $this->assertFileChanged(testAppPath('environments/.env.with-duplicates'), function (string $newContent) {
        return ! str_contains($newContent, 'APP_NAME');
    });
});

it('does not remove entries with prefixes', function () {
    $this->addResettableFile(testAppPath('environments/.env.with-similarities'));
    $action = new PruneEnvironmentFile();
    $action(testAppPath('environments/.env.with-similarities'), collect(['APP_NAME']));

    $this->assertFileChanged(testAppPath('environments/.env.with-similarities'), function (string $newContent) {
        return str_contains($newContent, 'APP_NAME_SHORT=')
            && str_contains($newContent, 'LONG_APP_NAME=');
    });
});
