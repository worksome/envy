<?php

declare(strict_types=1);

namespace Worksome\Envsync\Actions;

use Dotenv\Parser\Entry;
use Dotenv\Parser\Parser;
use Illuminate\Support\Collection;
use Worksome\Envsync\Contracts\Actions\ReadsEnvironmentFile;
use Worksome\Envsync\Support\EnvironmentVariable;

use function Safe\file_get_contents;

final class ReadEnvironmentFile implements ReadsEnvironmentFile
{
    public function __invoke(string $envFilePath): Collection
    {
        $parser = new Parser();
        $entries = $parser->parse(file_get_contents($envFilePath));

        return collect($entries)->map(fn (Entry $entry) => new EnvironmentVariable(
            $entry->getName(),
            $entry->getValue()->get()->getChars()
        ));
    }
}
