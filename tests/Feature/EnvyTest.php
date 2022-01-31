<?php

use Worksome\Envy\Envy;

it('can determine if a config file is unpublished', function () {
    $envy = $this->app->make(Envy::class);

    expect($envy->hasPublishedConfigFile())->toBeFalse();
})->group('withoutPublishedConfigFile');

it('can determine if a config file is published', function () {
    $envy = $this->app->make(Envy::class);

    expect($envy->hasPublishedConfigFile())->toBeTrue();
});
