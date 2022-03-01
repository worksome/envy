<?php

declare(strict_types=1);

namespace Worksome\Envy\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Worksome\Envy\Commands\Concerns\HasUsefulConsoleMethods;

final class InstallCommand extends Command
{
    use HasUsefulConsoleMethods;

    protected $signature = 'envy:install';

    protected $description = 'Publish Envy\'s config file to your project.';

    public function handle(): int
    {
        $this->call('vendor:publish', ['--tag' => 'envy-config']);

        if (!File::exists('.env.example')) {
            if (
                $this->confirm('Do you want to create a .env.example file? This file will be used to store your configuration data.')
            ) {
                $envExample =  Http::get('https://raw.githubusercontent.com/laravel/laravel/9.x/.env.example')->body();
                File::put('.env.example', $envExample);
            } else {
                $this->warn('You will need to create a .env.example file to store your configuration data.');
            }
        }

        $this->information('Alright, Envy is ready to rock! Get started with either `php artisan envy:sync` or `php artisan envy:prune`.');

        return self::SUCCESS;
    }
}
