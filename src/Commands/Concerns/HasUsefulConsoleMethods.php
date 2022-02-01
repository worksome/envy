<?php

declare(strict_types=1);

namespace Worksome\Envy\Commands\Concerns;

use Illuminate\Console\Command;

use function Termwind\render;

/**
 * @mixin Command
 */
trait HasUsefulConsoleMethods
{
    private function askUserToStarRepository(): void
    {
        render('
            <a href="https://github.com/worksome/envy" class="my-1 px-1 py-1 bg-blue-500 font-bold w-full text-center">
                ⭐️ If you like Envy, show your support by starring the repository! ⭐️
            </a>
        ');
    }
}
