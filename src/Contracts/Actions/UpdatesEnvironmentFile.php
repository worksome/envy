<?php

declare(strict_types=1);

namespace Worksome\Envy\Contracts\Actions;

use Illuminate\Support\Collection;
use Worksome\Envy\Support\EnvironmentCall;

interface UpdatesEnvironmentFile
{
    /**
     * @param string                           $filePath
     * @param Collection<int, EnvironmentCall> $environmentCalls
     */
    public function __invoke(string $filePath, Collection $environmentCalls): void;
}
