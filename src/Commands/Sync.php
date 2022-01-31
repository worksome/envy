<?php

declare(strict_types=1);

namespace Worksome\Envsync\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Worksome\Envsync\Contracts\Actions\FiltersEnvironmentCalls;
use Worksome\Envsync\Contracts\Actions\FindsEnvironmentCalls;
use Worksome\Envsync\Contracts\Actions\UpdatesEnvironmentFile;
use Worksome\Envsync\Contracts\Finder;
use Worksome\Envsync\Support\EnvironmentCall;

class Sync extends Command
{
    public $signature = 'envsync:sync
        {--dry : Run without making actual changes to the env files to see which variables will be added.}
    ';

    public $description = 'Sync your configured environment files based on calls to env.';

    public function handle(
        Finder                  $finder,
        FindsEnvironmentCalls   $findEnvironmentCalls,
        FiltersEnvironmentCalls $filterEnvironmentCalls,
        UpdatesEnvironmentFile  $updateEnvironmentFile,
    ): int
    {
        $allEnvironmentCalls = $this->environmentCalls($finder->configFilePaths(), $findEnvironmentCalls);

        /** @var Collection<string, Collection<int, EnvironmentCall>> $pendingUpdates */
        $pendingUpdates = collect($finder->environmentFilePaths())
            ->flip()
            // @phpstan-ignore-next-line
            ->map(fn(int $index, string $path) => $filterEnvironmentCalls($path, $allEnvironmentCalls))
            ->filter(fn(Collection $environmentCalls) => $environmentCalls->isNotEmpty());

        if ($pendingUpdates->isEmpty()) {
            $this->line('  There are no changes to sync!');
            return self::SUCCESS;
        }

        $this->printPendingUpdates($pendingUpdates);

        if ($this->option('dry')) {
            return self::FAILURE;
        }

        $pendingUpdates->each(fn(Collection $environmentCalls, string $path) => $updateEnvironmentFile(
            $path,
            $environmentCalls
        ));

        return self::SUCCESS;
    }

    /**
     * @param array<int, string> $filePaths
     * @param FindsEnvironmentCalls $findEnvironmentVariables
     * @return Collection<int, EnvironmentCall>
     */
    private function environmentCalls(array $filePaths, FindsEnvironmentCalls $findEnvironmentVariables): Collection
    {
        // @phpstan-ignore-next-line
        return collect($filePaths)
            ->map(fn(string $path) => $findEnvironmentVariables($path))
            ->flatten();
    }

    /**
     * Outputs pending updates to the console.
     *
     * @param Collection<string, Collection<int, EnvironmentCall>> $pendingUpdates
     */
    private function printPendingUpdates(Collection $pendingUpdates): void
    {
        $pendingUpdates->each(function (Collection $environmentCalls, string $path) {
            $this->line("  Updates for {$path}");
            $this->newLine();
            $environmentCalls->each(fn(EnvironmentCall $call) => $this->line("  - {$call->getKey()}"));
            $this->newLine(2);
        });
    }
}
