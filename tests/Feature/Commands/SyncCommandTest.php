<?php

use Worksome\Envy\Contracts\Actions\FindsEnvironmentCalls;

it('updates the .env file with missing keys', function () {
    $this->artisan('envy:sync', ['--force' => true])
        ->assertSuccessful();

    $this->assertFileChanged(testAppPath('.env.example'));

    expect(readEnvironmentFile())->toHaveCount(7);
});

it('can perform a dry run', function () {
    $this->artisan('envy:sync', ['--dry' => true, '--force' => true])
        ->assertFailed();

    $this->assertFileNotChanged(testAppPath('.env.example'));
});

it('returns success if performing a dry run but no changes are required', function () {
    $this->artisan('envy:sync', ['--dry' => true, '--force' => true])
        ->assertSuccessful();
})->shouldUseAction(FindsEnvironmentCalls::class, collect());

it('asks for confirmation before making the changes', function () {
    $this->artisan('envy:sync')
        ->expectsChoice('How would you like to handle these updates?', 'Cancel', [
            'Add to environment file',
            'Add to blacklist',
            'Cancel'
        ]);

    $this->assertFileNotChanged(testAppPath('.env.example'));
});

it('does not update blacklisted keys', function () {
    config()->set('envy.blacklist', ['APP_TITLE', 'APP_META']);

    $this->artisan('envy:sync', ['--force' => true])
        ->assertSuccessful();

    $this->assertFileNotChanged(testAppPath('.env.example'));
});

it('does not show the "Add to blacklist" option if the config file is unpublished', function () {
    $this->artisan('envy:sync')
        ->expectsChoice('How would you like to handle these updates?', 'Cancel', [
            'Add to environment file',
            'Cancel'
        ]);
})->group('withoutPublishedConfigFile');

it('can add entries to the blacklist automatically', function () {
    $this->artisan('envy:sync')
        ->expectsChoice('How would you like to handle these updates?', 'Add to blacklist', [
            'Add to environment file',
            'Add to blacklist',
            'Cancel'
        ]);

    $this->assertFileChanged(testAppPath('config/envy.php'), function ($newContent) {
        return str_contains($newContent, '\'APP_TITLE\',') && str_contains($newContent, '\'APP_META\',');
    });
});
