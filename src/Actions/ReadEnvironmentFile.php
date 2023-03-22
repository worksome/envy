<?php

declare(strict_types=1);

namespace Worksome\Envy\Actions;

use Dotenv\Parser\Entry;
use Dotenv\Parser\Parser;
use Illuminate\Support\Collection;
use Worksome\Envy\Contracts\Actions\ReadsEnvironmentFile;
use Worksome\Envy\Exceptions\EnvironmentFileNotFoundException;
use Worksome\Envy\Support\EnvironmentVariable;

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
        if (! file_exists($envFilePath)) {
            throw new EnvironmentFileNotFoundException($envFilePath);
        }

        $content = file_get_contents($envFilePath);

        assert($content !== false);

        return $content;
    }
}
