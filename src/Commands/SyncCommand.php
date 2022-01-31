<?php

declare(strict_types=1);

namespace Worksome\Envy\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Worksome\Envy\Commands\Concerns\HasUsefulConsoleMethods;
use Worksome\Envy\Envy;
use Worksome\Envy\Support\EnvironmentCall;

use function Termwind\render;

final class SyncCommand extends Command
{
    use HasUsefulConsoleMethods;

    private const ACTION_ADD_TO_ENVIRONMENT_FILE = 'Add to environment file';
    private const ACTION_ADD_TO_BLACKLIST = 'Add to blacklist';
    private const ACTION_CANCEL = 'Cancel';

    public $signature = 'envy:sync
        {--path= : The path to a specific environment file to prune.}
        {--dry : Run without making actual changes to the .env files to see which variables will be added.}
        {--force : Run without asking for confirmation.}
    ';

    public $description = 'Sync your configured .env files based on calls to env in config files.';

    public function handle(Envy $envy, Repository $config): int
    {
        $pendingUpdates = $envy->pendingUpdates(
            $envy->environmentCalls(boolval($config->get('envy.exclude_calls_with_defaults', false))),
            $this->option('path') ? [strval($this->option('path'))] : null,
        );

        if ($pendingUpdates->isEmpty()) {
            render('<div class="px-1 py-1 bg-green-500 font-bold">There are no variables to sync!</div>');
            return self::SUCCESS;
        }

        $this->printPendingUpdates($pendingUpdates);

        if ($this->option('dry')) {
            return self::FAILURE;
        }

        match ($this->askWhatWeShouldDoNext($envy->hasPublishedConfigFile())) {
            self::ACTION_ADD_TO_BLACKLIST => $this->addPendingUpdatesToBlacklist($envy, $pendingUpdates),
            self::ACTION_ADD_TO_ENVIRONMENT_FILE => $this->updateEnvironmentFiles($envy, $pendingUpdates),
            default => $this->warning('Sync cancelled'),
        };

        $this->askUserToStarRepository();

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

    private function askWhatWeShouldDoNext(bool $configFileHasBeenPublished): string
    {
        $options = collect([
            self::ACTION_ADD_TO_ENVIRONMENT_FILE => true,
            self::ACTION_ADD_TO_BLACKLIST => $configFileHasBeenPublished,
            self::ACTION_CANCEL => true,
        ])->filter()->keys()->all();

        return $this->option('force')
            ? self::ACTION_ADD_TO_ENVIRONMENT_FILE
            : strval($this->choice(
                'How would you like to handle these updates?',
                $options,
                self::ACTION_ADD_TO_ENVIRONMENT_FILE
            ));
    }

    /**
     * @param Collection<string, Collection<int, EnvironmentCall>> $pendingUpdates
     */
    private function addPendingUpdatesToBlacklist(Envy $envy, Collection $pendingUpdates): void
    {
        $envy->updateBlacklistWithPendingUpdates($pendingUpdates);
        $this->success('Blacklist updated!');
    }

    /**
     * @param Collection<string, Collection<int, EnvironmentCall>> $pendingUpdates
     */
    private function updateEnvironmentFiles(Envy $envy, Collection $pendingUpdates): void
    {
        $envy->updateEnvironmentFiles($pendingUpdates);
        $this->success('Environment variables added!');
    }
}
