<?php

declare(strict_types=1);

namespace Worksome\Envsync\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
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
        Finder $finder,
        FindsEnvironmentCalls $findEnvironmentVariables,
        UpdatesEnvironmentFile $updateEnvironmentFile,
    ): int {
        $environmentCalls = $this->environmentCalls($finder->configFilePaths(), $findEnvironmentVariables);

        collect($finder->environmentFilePaths())
            ->each(fn (string $path) => $updateEnvironmentFile($path, $environmentCalls));

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
            ->map(fn (string $path) => $findEnvironmentVariables($path))
            ->flatten();
    }
}
