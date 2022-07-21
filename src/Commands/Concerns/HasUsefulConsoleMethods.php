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
    private function success(string $message): self
    {
        render("
        <div class='mx-2 px-2 py-1 bg-green-500 font-bold'>$message</div>
        ");

        return $this;
    }

    private function warning(string $message): self
    {
        render("
        <div class='mx-2 px-2 py-1 bg-yellow-500 text-black font-bold'>$message</div>
        ");

        return $this;
    }

    private function information(string $message): self
    {
        render("
        <div class='mx-2 my-1 px-2 py-1 bg-blue-500 text-black font-bold text-gray-50'>$message</div>
        ");

        return $this;
    }

    private function askUserToStarRepository(): void
    {
        render('
            <footer class="mx-2 my-1 italic">
                ⭐️ <a href="https://github.com/worksome/envy">If you like Envy, show your support by starring the repository!</a> ⭐️
            </footer>
        ');
    }
}
