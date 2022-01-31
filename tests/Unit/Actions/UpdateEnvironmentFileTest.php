<?php

use Worksome\Envsync\Actions\FindEnvironmentCalls;
use Worksome\Envsync\Actions\FormatEnvironmentCall;
use Worksome\Envsync\Actions\ReadEnvironmentFile;
use Worksome\Envsync\Actions\UpdateEnvironmentFile;

it('updates the environment file with missing keys', function () {
    $findEnvironmentVariables = new FindEnvironmentCalls(defaultPhpParser());

    $action = new UpdateEnvironmentFile(new ReadEnvironmentFile(), new FormatEnvironmentCall());
    $action(
        testAppPath('.env.example'),
        $findEnvironmentVariables(testAppPath('config/app.php')),
    );

    $entries = readEnvironmentFile(testAppPath('.env.example'));
    expect($entries)->toHaveCount(7);
});
