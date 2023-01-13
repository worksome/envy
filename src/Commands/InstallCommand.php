<?php

declare(strict_types=1);

namespace Worksome\Envy\Commands;

use Illuminate\Console\Command;
use Worksome\Envy\Commands\Concerns\HasUsefulConsoleMethods;

final class InstallCommand extends Command
{
    use HasUsefulConsoleMethods;

    protected $signature = 'envy:install';

    protected $description = 'Publish Envy\'s config file to your project.';

    public function handle(): int
    {
        $this->call('vendor:publish', ['--tag' => 'envy-config']);

        $this->information(
            'Alright, Envy is ready to rock! Get started with either `php artisan envy:sync` or `php artisan envy:prune`.'
        );

        return self::SUCCESS;
    }
}
