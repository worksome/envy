<?php

declare(strict_types=1);

namespace Worksome\Envy\Actions;

use Illuminate\Support\Collection;
use Worksome\Envy\Contracts\Actions\FormatsEnvironmentCall;
use Worksome\Envy\Contracts\Actions\UpdatesEnvironmentFile;
use Worksome\Envy\Support\EnvironmentCall;

use function Safe\file_put_contents;

final class UpdateEnvironmentFile implements UpdatesEnvironmentFile
{
    public function __construct(private FormatsEnvironmentCall $formatEnvironmentCall)
    {
    }

    public function __invoke(string $filePath, Collection $environmentCalls): void
    {
        $content = $environmentCalls
            ->map(fn (EnvironmentCall $call) => ($this->formatEnvironmentCall)($call))
            ->join(PHP_EOL);

        file_put_contents($filePath, $content, FILE_APPEND);
    }
}
