<?php

declare(strict_types=1);

namespace Worksome\Envy\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Worksome\Envy\Contracts\Actions\FiltersEnvironmentCalls;
use Worksome\Envy\Contracts\Actions\UpdatesEnvironmentFile;
use Worksome\Envy\Contracts\Finder;
use Worksome\Envy\Envy;
use Worksome\Envy\Support\EnvironmentCall;

class Sync extends Command
{
    public $signature = 'envsync:sync
        {--dry : Run without making actual changes to the .env files to see which variables will be added.}
    ';

    public $description = 'Sync your configured .env files based on calls to env in config files.';

    public function handle(
        Envy $envsync,
        Finder $finder,
        FiltersEnvironmentCalls $filterEnvironmentCalls,
        UpdatesEnvironmentFile $updateEnvironmentFile,
    ): int {
        $allEnvironmentCalls = $envsync->environmentCalls();

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
