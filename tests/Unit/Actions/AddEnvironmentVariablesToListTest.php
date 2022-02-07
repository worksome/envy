<?php

use PhpParser\ErrorHandler;
use PhpParser\Parser;
use Worksome\Envy\Actions\AddEnvironmentVariablesToList;
use Worksome\Envy\Contracts\Actions\AddsEnvironmentVariablesToList;
use Worksome\Envy\Exceptions\ConfigFileNotFoundException;
use Worksome\Envy\Support\EnvironmentVariable;
use Worksome\Envy\Tests\Doubles\TestFinder;

it('throws an exception if the config file is unpublished', function (string $list) {
    $action = new AddEnvironmentVariablesToList(defaultPhpParser(), new TestFinder());
    $action(collect(), $list);
})
    ->with([AddsEnvironmentVariablesToList::EXCLUSIONS, AddsEnvironmentVariablesToList::INCLUSIONS])
    ->throws(ConfigFileNotFoundException::class)
    ->group('withoutPublishedConfigFile');

it('updates the config file with the given updates', function (string $list) {
    $action = new AddEnvironmentVariablesToList(defaultPhpParser(), new TestFinder());
    $action(collect([
        new EnvironmentVariable('FOO', 'BAR'),
        new EnvironmentVariable('BAZ', ''),
    ]), $list);

    $this->assertFileChanged(testAppPath('config/envy.php'), function ($newContents) {
        return str_contains($newContents, '\'FOO\'') && str_contains($newContents, '\'BAZ\'');
    });
})->with([AddsEnvironmentVariablesToList::EXCLUSIONS, AddsEnvironmentVariablesToList::INCLUSIONS]);

it('performs no changes if the parser returns null', function (string $list) {
    $parser = new class implements Parser {
        public function parse(string $code, ErrorHandler $errorHandler = null)
        {
            return null;
        }
    };

    $action = new AddEnvironmentVariablesToList($parser, new TestFinder());
    $action(collect([
        new EnvironmentVariable('FOO', 'BAR'),
        new EnvironmentVariable('BAZ', ''),
    ]), $list);

    $this->assertFileNotChanged(testAppPath('config/envy.php'));
})->with([AddsEnvironmentVariablesToList::EXCLUSIONS, AddsEnvironmentVariablesToList::INCLUSIONS]);

it('throws an exception if the given list key doesn\'t exist', function () {
    $action = new AddEnvironmentVariablesToList(defaultPhpParser(), new TestFinder());
    $action(collect([new EnvironmentVariable('FOO', '')]), 'foo');
})->throws(InvalidArgumentException::class);
