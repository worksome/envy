<?php

declare(strict_types=1);

namespace Worksome\Envy\Contracts\Actions;

use Worksome\Envy\Support\EnvironmentCall;

interface FormatsEnvironmentCall
{
    public function __invoke(EnvironmentCall $environmentCall): string;
}
