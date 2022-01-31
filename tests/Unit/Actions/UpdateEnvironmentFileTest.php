<?php

use Worksome\Envy\Actions\FindEnvironmentCalls;
use Worksome\Envy\Actions\FormatEnvironmentCall;
use Worksome\Envy\Actions\UpdateEnvironmentFile;

it('updates the environment file with given keys', function () {
    $findEnvironmentVariables = new FindEnvironmentCalls(defaultPhpParser());

    $action = new UpdateEnvironmentFile(new FormatEnvironmentCall());

    $action(
        testAppPath('.env.example'),
        $findEnvironmentVariables(testAppPath('config/app.php')),
    );

    $entries = readEnvironmentFile(testAppPath('.env.example'));
    expect($entries)->toHaveCount(14); // This action performs no filtering, so the result is 14.
});
