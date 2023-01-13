<?php

declare(strict_types=1);

namespace Worksome\Envy\Actions;

use Illuminate\Support\Collection;
use Worksome\Envy\Contracts\Actions\PrunesEnvironmentFile;

use function Safe\file_get_contents;
use function Safe\file_put_contents;
use function Safe\preg_replace;

final class PruneEnvironmentFile implements PrunesEnvironmentFile
{
    public function __invoke(string $filePath, Collection $pendingPrunes): void
    {
        // @phpstan-ignore-next-line
        $updatedContent = $pendingPrunes->reduce(function (string $content, string $environmentVariable) {
            $environmentVariable = preg_quote($environmentVariable);
            return preg_replace("/(#.*|\r\n?|\n)*^{$environmentVariable}=.*$/m", '', $content);
        }, file_get_contents($filePath));

        file_put_contents($filePath, $updatedContent);
    }
}
