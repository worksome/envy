<?php

use Worksome\Envsync\Contracts\Actions\UpdatesEnvironmentFile;

it('updates the .env file with missing keys', function () {
    $this->artisan('envsync:sync')
        ->assertSuccessful();
})->shouldUseAction(UpdatesEnvironmentFile::class);
