<?php

declare(strict_types=1);

namespace Worksome\Envsync\Contracts\Actions;

use Illuminate\Support\Collection;
use Worksome\Envsync\Support\EnvironmentVariable;

interface ReadsEnvironmentFile
{
    /**
     * @return Collection<int, EnvironmentVariable>
     */
    public function __invoke(string $envFilePath): Collection;
}
