<?php

declare(strict_types=1);

namespace Worksome\Envy\Contracts\Actions;

use Illuminate\Support\Collection;

interface PrunesEnvironmentFile
{
    /**
     * @param Collection<int, string> $pendingPrunes
     */
    public function __invoke(string $filePath, Collection $pendingPrunes): void;
}
