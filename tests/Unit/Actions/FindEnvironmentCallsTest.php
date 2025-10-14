<?php

use PhpParser\ErrorHandler;
use PhpParser\Parser;
use Worksome\Envy\Actions\FindEnvironmentCalls;
use Worksome\Envy\Support\EnvironmentCall;

it(
    'can return a collection of environment variables',
    function (bool $excludeVariablesWithDefaults, int $expectedCount) {
        $action = new FindEnvironmentCalls(defaultPhpParser());
        $variables = $action(__DIR__ . '/../../Application/config/app.php', $excludeVariablesWithDefaults);
    
        expect($variables)
            ->toBeCollection()
            ->toHaveCount($expectedCount)
            ->each->toBeInstanceOf(EnvironmentCall::class);
    }
)->with([
    'variables including defaults' => [false, 9],
    'variables excluding defaults' => [true, 3],
]);

it('contains the correct keys for each environment variable', function () {
    $action = new FindEnvironmentCalls(defaultPhpParser());
    $variableNames = $action(__DIR__ . '/../../Application/config/app.php')
        ->map(fn (EnvironmentCall $variable) => $variable->getKey())
        ->all();

    expect($variableNames)->toEqual([
        'APP_NAME',
        'APP_TITLE',
        'APP_NAME',
        'APP_DESCRIPTION',
        'APP_TITLE',
        'APP_META',
        'APP_ENV',
        'APP_DEBUG',
        'APP_URL',
    ]);
});

it('correctly retrieves comments', function () {
    $action = new FindEnvironmentCalls(defaultPhpParser());
    $appName = $action(__DIR__ . '/../../Application/config/app.php')->first();

    $normalize = fn (string $s) => str_replace(["\r\n", "\r"], "\n", $s);

    expect($normalize($appName->getComment()))->toBe($normalize(<<<'TXT'
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */
    TXT));
});

it('correctly handles function calls used as defaults', function () {
    $action = new FindEnvironmentCalls(defaultPhpParser());
    $appTitle = $action(__DIR__ . '/../../Application/config/app.php')->get(1);

    expect($appTitle->getDefault())->toBeNull();
});

it('correctly handles static method calls used as defaults', function () {
    $action = new FindEnvironmentCalls(defaultPhpParser());
    $appName = $action(__DIR__ . '/../../Application/config/app.php')->get(0);

    expect($appName->getDefault())->toBeNull();
});

it('correctly handles static ternary checks used as defaults', function () {
    $action = new FindEnvironmentCalls(defaultPhpParser());
    $appEnv = $action(__DIR__ . '/../../Application/config/app.php')->get(5);

    expect($appEnv->getDefault())->toBeNull();
});

it('correctly handles boolean values used as defaults', function () {
    $action = new FindEnvironmentCalls(defaultPhpParser());
    $appEnv = $action(__DIR__ . '/../../Application/config/app.php')->get(7);

    expect($appEnv->getDefault())->toEqual('false');
});

it('returns an empty collection if the parser returns null', function () {
    $parser = new class() implements Parser {
        public function parse(string $code, ErrorHandler|null $errorHandler = null): array|null
        {
            return null;
        }

        public function getTokens(): array
        {
            return [];
        }
    };

    $action = new FindEnvironmentCalls($parser);
    expect($action(__DIR__ . '/../../Application/config/app.php'))
        ->toBeCollection()
        ->toHaveCount(0);
});
