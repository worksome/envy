<?php

declare(strict_types=1);

namespace Worksome\Envy\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Worksome\Envy\Envy;
use Worksome\Envy\Support\EnvironmentCall;

use function Termwind\render;

class Sync extends Command
{
    public $signature = 'envy:sync
        {--dry : Run without making actual changes to the .env files to see which variables will be added.}
        {--force : Run without asking for confirmation.}
    ';

    public $description = 'Sync your configured .env files based on calls to env in config files.';

    public function handle(Envy $envy): int
    {
        $pendingUpdates = $envy->pendingUpdates($envy->environmentCalls());

        if ($pendingUpdates->isEmpty()) {
            render('<div class="px-1 py-1 bg-green-500 font-bold">There are no changes to sync!</div>');
            return self::SUCCESS;
        }

        $this->printPendingUpdates($pendingUpdates);

        if ($this->option('dry')) {
            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm('Are you sure you want to continue?')) {
            return self::SUCCESS;
        }

        $envy->updateEnvironmentFiles($pendingUpdates);

        return self::SUCCESS;
    }

    /**
     * Outputs pending updates to the console.
     *
     * @param Collection<string, Collection<int, EnvironmentCall>> $pendingUpdates
     */
    private function printPendingUpdates(Collection $pendingUpdates): void
    {
        render(Blade::render('
        <div>
        @foreach($pendingUpdates as $path => $environmentCalls)
            <div class="my-1">
                <div class="px-2 py-1 w-full bg-green-500 font-bold">
                    <span class="text-left w-1/2">
                        {{ $environmentCalls->count() }} {{ Str::plural("update", $environmentCalls->count()) }} for {{ Str::after($path, base_path()) }}
                    </span>
                    <span class="text-right w-1/2">
                        {{ $loop->iteration }}/{{ $loop->count }}
                    </span>
                </div>
                <ul class="mx-1 mt-1 space-y-1">
                    @foreach($environmentCalls as $environmentCall)
                        <li>{{ $environmentCall->getKey() }}</li>
                    @endforeach
                </ul>
            </div>
        @endforeach
        </div>
        ', ['pendingUpdates' => $pendingUpdates]));
    }
}
