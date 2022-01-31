<?php

declare(strict_types=1);

namespace Worksome\Envsync\Actions\UpdateEnvironmentFile;

use Illuminate\Support\Collection;
use Worksome\Envsync\Contracts\Actions\FormatsEnvironmentCall;
use Worksome\Envsync\Contracts\Actions\UpdatesEnvironmentFile;
use Worksome\Envsync\Support\EnvironmentCall;

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
