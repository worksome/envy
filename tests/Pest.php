<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Worksome\Envy\Actions\ReadEnvironmentFile;
use Worksome\Envy\Tests\Unit;
use Worksome\Envy\Tests\Feature;

uses(Feature\TestCase::class)->in(__DIR__ . '/Feature');
uses(Unit\TestCase::class)->in(__DIR__ . '/Unit');

function defaultPhpParser(): Parser
{
    return (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
}

function readEnvironmentFile(string $filePath = null): Collection
{
    $filePath ??= testAppPath('.env.example');
    $readEnvironmentFile = new ReadEnvironmentFile();

    return $readEnvironmentFile($filePath);
}

function testAppPath(string $path = ''): string
{
    return Str::of(__DIR__)
        ->append('/Application')
        ->append(Str::start($path, '/'))
        ->replace('/', DIRECTORY_SEPARATOR)
        ->__toString();
}
