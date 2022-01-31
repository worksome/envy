<?php

declare(strict_types=1);

namespace Worksome\Envsync\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Worksome\Envsync\Envsync
 */
class Envsync extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'envsync';
    }
}
