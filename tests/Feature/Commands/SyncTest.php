<?php

it('updates the .env file with missing keys', function () {
    $this->artisan('envsync:sync')
        ->assertSuccessful();

    $this->assertFileChanged(testAppPath('.env.example'));

    expect(readEnvironmentFile())->toHaveCount(7);
});

it('can perform a dry run', function () {
    $this->artisan('envsync:sync', ['--dry' => true])
        ->assertFailed();

    $this->assertFileNotChanged(testAppPath('.env.example'));
});
