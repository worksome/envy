<?php

declare(strict_types=1);

namespace Worksome\Envy\Commands;

use Illuminate\Console\Command;

final class InstallCommand extends Command
{
    protected $signature = 'envy:install';

    protected $description = 'Publish Envy\'s config file to your project.';

    public function handle(): int
    {
        $this->call('vendor:publish', ['--tag' => 'envy-config']);

        return self::SUCCESS;
    }
}
