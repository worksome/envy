<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Worksome\Envsync\Actions\ReadEnvironmentFile;
use Worksome\Envsync\Tests\Unit;
use Worksome\Envsync\Tests\Feature;

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
        ->__toString();
}
