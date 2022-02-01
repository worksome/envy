<?php

use Worksome\Envy\Contracts\Actions\FindsEnvironmentCalls;

it('updates the .env file with missing keys', function () {
    $this->artisan('envy:sync', ['--force' => true])
        ->assertSuccessful();

    $this->assertFileChanged(testAppPath('.env.example'));

    expect(readEnvironmentFile())->toHaveCount(6);
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
        ->expectsQuestion('Are you sure you want to continue?', false);

    $this->assertFileNotChanged(testAppPath('.env.example'));
});

it('does not update blacklisted keys', function () {
    config()->set('envy.blacklist', ['APP_TITLE', 'APP_DESCRIPTION']);

    $this->artisan('envy:sync', ['--force' => true])
        ->assertSuccessful();

    $this->assertFileNotChanged(testAppPath('.env.example'));
});
