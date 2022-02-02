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
