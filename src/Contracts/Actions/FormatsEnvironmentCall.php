<?php

declare(strict_types=1);

namespace Worksome\Envsync\Contracts\Actions;

use Worksome\Envsync\Support\EnvironmentCall;

interface FormatsEnvironmentCall
{
    public function __invoke(EnvironmentCall $environmentCall): string;
}
