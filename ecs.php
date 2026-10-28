<?php

declare(strict_types=1);

use Worksome\CodingStyle\WorksomeEcsConfig;

return WorksomeEcsConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/config',
    ]);
