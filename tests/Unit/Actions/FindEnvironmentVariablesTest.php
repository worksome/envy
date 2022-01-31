<?php

use PhpParser\ParserFactory;
use Worksome\Envsync\Actions\FindEnvironmentVariables;
use Worksome\Envsync\Support\EnvironmentVariable;

it('can return a collection of environment variables', function (bool $excludeVariablesWithDefaults, int $expectedCount) {
    $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
    $action = new FindEnvironmentVariables($parser);
    $variables = $action(__DIR__ . '/../../Application/config/app.php', $excludeVariablesWithDefaults);

    expect($variables)
        ->toBeCollection()
        ->toHaveCount($expectedCount)
        ->each->toBeInstanceOf(EnvironmentVariable::class);
})->with([
    [false, 6],
    [true, 2],
]);
