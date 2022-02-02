<?php

declare(strict_types=1);

namespace Worksome\Envy\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Worksome\Envy\Envy;

use function Termwind\render;

final class PruneCommand extends Command
{
    private const ACTION_PRUNE_ENVIRONMENT_FILE = 'Prune environment file';
    private const ACTION_ADD_TO_WHITELIST = 'Add to whitelist';
    private const ACTION_CANCEL = 'Cancel';

    protected $signature = 'envy:prune
    {--dry : Run without making actual changes to the .env files to see which variables will be pruned.}
    {--path : The path to a specific environment file to prune.}
    {--force : Run without asking for confirmation.}
    ';

    protected $description = 'Prune environment variables that aren\'t found in your config files.';

    public function handle(Envy $envy): int
    {
        $pendingPrunes = $envy->pendingPrunes(
            $envy->environmentCalls(),
            $this->option('path') ? [strval($this->option('path'))] : null
        );

        if ($pendingPrunes->isEmpty()) {
            render('<div class="px-1 py-1 bg-green-500 font-bold">There are no variables to prune!</div>');
            return self::SUCCESS;
        }

        $this->printPendingPrunes($pendingPrunes);

        if ($this->option('dry')) {
            return self::FAILURE;
        }

        match ($this->askWhatWeShouldDoNext($envy->hasPublishedConfigFile())) {
            self::ACTION_ADD_TO_WHITELIST => $envy->updateWhitelistWithPendingPrunes($pendingPrunes),
            self::ACTION_PRUNE_ENVIRONMENT_FILE => $envy->pruneEnvironmentFiles($pendingPrunes),
            default => render('<div class="px-1 py-1 bg-yellow-500 text-black font-bold">Prune cancelled</div>'),
        };

        return self::SUCCESS;
    }

    /**
     * @param Collection<string, Collection<int, string>> $pendingPrunes
     */
    private function printPendingPrunes(Collection $pendingPrunes): void
    {
        render(Blade::render('
        <div>
        @foreach($pendingPrunes as $path => $environmentVariables)
        <div class="my-1">
            <div class="px-2 py-1 w-full bg-green-500 font-bold">
                <span class="text-left w-1/2">
                    {{ $environmentVariables->count() }} {{ Str::plural("variable", $environmentVariables->count()) }} to remove for {{ Str::after($path, base_path()) }}
                </span>
                <span class="text-right w-1/2">
                    {{ $loop->iteration }}/{{ $loop->count }}
                </span>
            </div>
            <ul class="mx-1 mt-1 space-y-1">
                @foreach($environmentVariables as $environmentVariable)
                    <li>{{ $environmentVariable }}</li>
                @endforeach
            </ul>
        </div>
        @endforeach
        </div>
        ', ['pendingPrunes' => $pendingPrunes]));
    }

    private function askWhatWeShouldDoNext(bool $configFileHasBeenPublished): string
    {
        $options = collect([
            self::ACTION_PRUNE_ENVIRONMENT_FILE => true,
            self::ACTION_ADD_TO_WHITELIST => $configFileHasBeenPublished,
            self::ACTION_CANCEL => true,
        ])->filter()->keys()->all();

        return $this->option('force')
            ? self::ACTION_PRUNE_ENVIRONMENT_FILE
            : strval($this->choice(
                'How would you like to handle pruning?',
                $options,
                self::ACTION_PRUNE_ENVIRONMENT_FILE
            ));
    }
}
