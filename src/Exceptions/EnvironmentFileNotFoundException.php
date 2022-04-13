<?php

declare(strict_types=1);

namespace Worksome\Envy\Exceptions;

use Exception;

final class EnvironmentFileNotFoundException extends Exception
{
    public function __construct(string $environmentFileName)
    {
        parent::__construct("We were unable to locate [$environmentFileName]. Does it exist in your project?");
    }
}
