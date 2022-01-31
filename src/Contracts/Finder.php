<?php

declare(strict_types=1);

namespace Worksome\Envsync\Contracts;

interface Finder
{
    public function configDirectory(): string;

    public function envExampleDirectory(): string;
}
