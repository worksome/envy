<?php

declare(strict_types=1);

namespace Worksome\Envy\Contracts\Actions;

use Illuminate\Support\Collection;
use Worksome\Envy\Support\EnvironmentVariable;

interface ReadsEnvironmentFile
{
    /**
     * @return Collection<int, EnvironmentVariable>
     */
    public function __invoke(string $envFilePath): Collection;
}
