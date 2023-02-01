<?php

declare(strict_types=1);

namespace Worksome\Envy\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Collection;
use Illuminate\View\Compilers\BladeCompiler;
use Worksome\Envy\Commands\Concerns\HasUsefulConsoleMethods;
use Worksome\Envy\Envy;
use Worksome\Envy\Exceptions\EnvironmentFileNotFoundException;
use Worksome\Envy\Support\EnvironmentCall;

use function Termwind\render;

final class SyncCommand extends Command
{
    use HasUsefulConsoleMethods;

    private const ACTION_ADD_TO_ENVIRONMENT_FILE = 'Add to environment file';
    private const ACTION_ADD_TO_EXCLUSIONS = 'Add to exclusions';
    private const ACTION_CANCEL = 'Cancel';

    public $signature = 'envy:sync
        {--path= : The path to a specific environment file to prune.}
        {--dry : Run without making actual changes to the .env files to see which variables will be added.}
        {--force : Run without asking for confirmation.}
    ';

    public $description = 'Sync your configured .env files based on calls to env in config files.';

    public function handle(Envy $envy, Repository $config, BladeCompiler $blade): int
    {
        try {
            $pendingUpdates = $this->getPendingPrunes($envy, $config);
        } catch (EnvironmentFileNotFoundException $exception) {
            $this->warning($exception->getMessage());

            return self::INVALID;
        }

        if ($pendingUpdates->isEmpty()) {
            render('<div class="mx-2 my-1 px-2 py-1 bg-green-500 font-bold">There are no variables to sync!</div>');
            return self::SUCCESS;
        }

        $this->printPendingUpdates($pendingUpdates, $blade);

        if ($this->option('dry')) {
            return self::FAILURE;
        }

        match ($this->askWhatWeShouldDoNext($envy->hasPublishedConfigFile())) {
            self::ACTION_ADD_TO_EXCLUSIONS => $this->addPendingUpdatesToExclusions($envy, $pendingUpdates),
            self::ACTION_ADD_TO_ENVIRONMENT_FILE => $this->updateEnvironmentFiles($envy, $pendingUpdates),
            default => $this->warning('Sync cancelled'),
        };

        $this->askUserToStarRepository();

        return self::SUCCESS;
    }

    /**
     * @return Collection<string, Collection<int, EnvironmentCall>>
     *
     * @throws EnvironmentFileNotFoundException
     */
    private function getPendingPrunes(Envy $envy, Repository $config): Collection
    {
        return $envy->pendingUpdates(
            $envy->environmentCalls(boolval($config->get('envy.exclude_calls_with_defaults', false))),
            $this->option('path') ? [strval($this->option('path'))] : null,
        );
    }

    /**
     * Outputs pending updates to the console.
     *
     * @param Collection<string, Collection<int, EnvironmentCall>> $pendingUpdates
     */
    private function printPendingUpdates(Collection $pendingUpdates, BladeCompiler $blade): void
    {
        render($blade->render(<<<'HTML'
            <div class="mx-2 my-1 space-y-1">
                @foreach ($pendingUpdates as $path => $environmentCalls)
                    <div class="space-y-1">
                        <div class="px-2 py-1 w-full max-w-90 flex justify-between bg-green-500 font-bold">
                            <span>
                                <b>{{ $environmentCalls->count() }}</b> {{ Str::plural("update", $environmentCalls->count()) }} for {{ Str::after($path, base_path()) }}
                            </span>
                            <span>
                                {{ $loop->iteration }}/{{ $loop->count }}
                            </span>
                        </div>
                        <div>
                            @foreach ($environmentCalls as $environmentCall)
                                <div><span class="text-gray">⇁</span> {{ $environmentCall->getKey() }}</div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        HTML, ['pendingUpdates' => $pendingUpdates]));
    }

    private function askWhatWeShouldDoNext(bool $configFileHasBeenPublished): string
    {
        $options = collect([
            self::ACTION_ADD_TO_ENVIRONMENT_FILE => true,
            self::ACTION_ADD_TO_EXCLUSIONS => $configFileHasBeenPublished,
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
    private function addPendingUpdatesToExclusions(Envy $envy, Collection $pendingUpdates): void
    {
        $envy->updateExclusionsWithPendingUpdates($pendingUpdates);
        $this->success('Exclusions updated!');
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
