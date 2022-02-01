<?php

declare(strict_types=1);

namespace Worksome\Envy\Exceptions;

use Exception;

final class ConfigFileNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('You haven\'t published the envy config file, so this action cannot be performed.');
    }
}
