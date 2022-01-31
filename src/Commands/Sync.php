<?php

declare(strict_types=1);

namespace Worksome\Envsync\Commands;

use Illuminate\Console\Command;

class Sync extends Command
{
    public $signature = 'envsync:sync';

    public $description = 'Sync your .env.example file based on your config file values.';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
