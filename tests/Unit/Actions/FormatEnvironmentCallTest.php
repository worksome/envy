<?php

use Worksome\Envy\Actions\FormatEnvironmentCall;
use Worksome\Envy\Support\EnvironmentCall;

it('can format an environment call', function (array $options, string $key, mixed $default, string $expectedResult) {
    $call = new EnvironmentCall(
        testAppPath('config/app.php'),
        1,
        $key,
        $default,
    );

    $action = new FormatEnvironmentCall(...$options);
    $result = $action($call);

    expect($result)->toBe($expectedResult);
})->with([
    'without default' => [[false, false, false], 'FOO_BAR', null, 'FOO_BAR='],
    'without default when showing defaults' => [[false, false, true], 'FOO_BAR', null, 'FOO_BAR='],
    'with default' => [[false, false, false], 'FOO_BAR', 'BAZ', 'FOO_BAR='],
    'with default when showing defaults' => [[false, false, true], 'FOO_BAR', 'BAZ', 'FOO_BAR=BAZ'],
]);

it('can display a location hint', function () {
    $path = testAppPath('config/app.php');
    $call = new EnvironmentCall($path, 50, 'FOO_BAR');

    $action = new FormatEnvironmentCall(false, true, false);
    $result = $action($call);

    expect($result)->toBe(<<<TXT
    # See {$path}::50
    FOO_BAR=
    TXT);
});

it('can display a docblock comment', function () {
    $path = testAppPath('config/app.php');
    $call = new EnvironmentCall($path, 1, 'FOO_BAR', null, <<<TXT
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
    TXT);

    $action = new FormatEnvironmentCall(true, false, false);
    $result = $action($call);

    expect($result)->toBe(<<<TXT
    #
    #|--------------------------------------------------------------------------
    #| Application Name
    #|--------------------------------------------------------------------------
    #|
    #| This value is the name of your application. This value is used when the
    #| framework needs to place the application's name in a notification or
    #| any other location as required by the application or its packages.
    #|
    #
    FOO_BAR=
    TXT);
});

it('can display an inline comment', function () {
    $path = testAppPath('config/app.php');
    $call = new EnvironmentCall($path, 1, 'FOO_BAR', null, <<<TXT
    // This is foo bar
    TXT);

    $action = new FormatEnvironmentCall(true, false, false);
    $result = $action($call);

    expect($result)->toBe(<<<TXT
    # This is foo bar
    FOO_BAR=
    TXT);
});

it('can correctly display everything together', function () {
    $path = testAppPath('config/app.php');
    $call = new EnvironmentCall($path, 20, 'FOO_BAR', 'BAZ', <<<TXT
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
    TXT);

    $action = new FormatEnvironmentCall(true, true, true);
    $result = $action($call);

    expect($result)->toBe(<<<TXT
    #
    #|--------------------------------------------------------------------------
    #| Application Name
    #|--------------------------------------------------------------------------
    #|
    #| This value is the name of your application. This value is used when the
    #| framework needs to place the application's name in a notification or
    #| any other location as required by the application or its packages.
    #|
    #
    # See {$path}::20
    FOO_BAR=BAZ
    TXT);
});

it('places quotes around default values if there is whitespace', function () {
    $path = testAppPath('config/app.php');
    $call = new EnvironmentCall($path, 1, 'FOO_BAR', 'Foo Bar Baz');

    $action = new FormatEnvironmentCall(false, false, true);
    $result = $action($call);

    expect($result)->toBe(<<<TXT
    FOO_BAR="Foo Bar Baz"
    TXT);
});
