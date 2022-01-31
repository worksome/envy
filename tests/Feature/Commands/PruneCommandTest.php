<?php

it('prunes an environment file with additional entries', function () {
    $this->artisan('envy:prune', ['--force' => true]);

    $this->assertFileChanged(testAppPath('.env.example'), function (string $newContent) {
        return ! str_contains($newContent, 'MIX_URL');
    });
});

it('returns a success code if there are no variables to prune', function () {
    $this->addResettableFile(testAppPath('environments/.env.empty'));
    $this->artisan('envy:prune', [
        '--force' => true,
        '--path' => testAppPath('environments/.env.empty')
    ])->assertSuccessful();

    $this->assertFileNotChanged(testAppPath('environments/.env.empty'));
});

it('returns a failure code if performing a dry run and there are variables to prune', function () {
    $this->artisan('envy:prune', ['--dry' => true])->assertFailed();

    $this->assertFileNotChanged(testAppPath('.env.example'));
});

it('will ask the user to select an option before progressing', function () {
    $this->artisan('envy:prune')
        ->expectsChoice('How would you like to handle pruning?', 'Prune environment file', [
            'Prune environment file',
            'Add to whitelist',
            'Cancel'
        ]);

    $this->assertFileChanged(testAppPath('.env.example'));
});

it('will not show the Add to whitelist option if the envy config file has not been published', function () {
    $this->artisan('envy:prune')
        ->expectsChoice('How would you like to handle pruning?', 'Cancel', [
            'Prune environment file',
            'Cancel'
        ]);
})->group('withoutPublishedConfigFile');

it('can add the pruned variables to the config whitelist', function () {
    $this->artisan('envy:prune')
        ->expectsChoice('How would you like to handle pruning?', 'Add to whitelist', [
            'Prune environment file',
            'Add to whitelist',
            'Cancel'
        ]);

    $this->assertFileNotChanged(testAppPath('.env.example'));
    $this->assertFileChanged(testAppPath('config/envy.php'), function (string $newContent) {
        return str_contains($newContent, 'MIX_URL');
    });
});
