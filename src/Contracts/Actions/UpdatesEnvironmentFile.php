<?php

declare(strict_types=1);

namespace Worksome\Envsync\Contracts\Actions;

use Illuminate\Support\Collection;
use Worksome\Envsync\Support\EnvironmentCall;

interface UpdatesEnvironmentFile
{
    /**
     * @param string $filePath
     * @param Collection<int, EnvironmentCall> $environmentCalls
     */
    public function __invoke(string $filePath, Collection $environmentCalls): void;
}
