<?php

declare(strict_types=1);

namespace Worksome\Envy\Actions;

use Dotenv\Parser\Entry;
use Dotenv\Parser\Parser;
use Illuminate\Support\Collection;
use Throwable;
use Worksome\Envy\Contracts\Actions\ReadsEnvironmentFile;
use Worksome\Envy\Exceptions\EnvironmentFileNotFoundException;
use Worksome\Envy\Support\EnvironmentVariable;

use function Safe\file_get_contents;

final class ReadEnvironmentFile implements ReadsEnvironmentFile
{
    public function __invoke(string $envFilePath): Collection
    {
        $parser = new Parser();
        $entries = $parser->parse($this->getFileContents($envFilePath));

        return collect($entries)->map(fn (Entry $entry) => new EnvironmentVariable(
            $entry->getName(),
            $entry->getValue()->get()->getChars()
        ));
    }

    /**
     * @throws EnvironmentFileNotFoundException
     */
    private function getFileContents(string $envFilePath): string
    {
        try {
            return file_get_contents($envFilePath);
        } catch (Throwable) {
            throw new EnvironmentFileNotFoundException($envFilePath);
        }
    }
}
