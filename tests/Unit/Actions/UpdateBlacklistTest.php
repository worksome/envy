<?php

use Worksome\Envy\Actions\UpdateBlacklist;
use Worksome\Envy\Exceptions\ConfigFileNotFoundException;
use Worksome\Envy\Support\EnvironmentVariable;

it('throws an exception if the config file is unpublished', function () {
    $action = new UpdateBlacklist(defaultPhpParser(), testAppPath('config/envy.php'));
    $action(collect());
})
    ->throws(ConfigFileNotFoundException::class)
    ->group('withoutPublishedConfigFile');

it('updates the config file with the given updates', function () {
    $action = new UpdateBlacklist(defaultPhpParser(), testAppPath('config/envy.php'));
    $action(collect([
        new EnvironmentVariable('FOO', 'BAR'),
        new EnvironmentVariable('BAZ', ''),
    ]));

    $this->assertFileChanged(testAppPath('config/envy.php'), function ($newContents) {
        return str_contains($newContents, '\'FOO\',') && str_contains($newContents, '\'BAZ\',');
    });
});
