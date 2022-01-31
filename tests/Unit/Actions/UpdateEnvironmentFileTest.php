<?php

use Worksome\Envsync\Actions\FindEnvironmentCalls;
use Worksome\Envsync\Actions\FormatEnvironmentCall;
use Worksome\Envsync\Actions\UpdateEnvironmentFile\UpdateEnvironmentFile;

it('updates the environment file with given keys', function () {
    $findEnvironmentVariables = new FindEnvironmentCalls(defaultPhpParser());

    $action = new UpdateEnvironmentFile(new FormatEnvironmentCall());

    $action(
        testAppPath('.env.example'),
        $findEnvironmentVariables(testAppPath('config/app.php')),
    );

    $entries = readEnvironmentFile(testAppPath('.env.example'));
    expect($entries)->toHaveCount(13); // This action performs no filtering, so the result is 13.
});
