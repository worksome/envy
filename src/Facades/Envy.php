<?php

declare(strict_types=1);

namespace Worksome\Envy\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Worksome\Envy\Envy
 */
class Envy extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'envy';
    }
}
