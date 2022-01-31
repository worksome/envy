<?php

namespace Worksome\Envsync\Tests\Concerns;

use BadMethodCallException;
use Closure;
use PHPUnit\Framework\ExpectationFailedException;

use function Safe\file_get_contents;
use function Safe\file_put_contents;

trait ResetsTestFiles
{
    private array $fileContents = [];

    public function setUpResetsTestFiles(): void
    {
        foreach ($this->getFilesToReset() as $path) {
            $this->fileContents[$path] = file_get_contents($path);
        }
    }

    public function tearDownResetsTestFiles(): void
    {
        foreach ($this->getFilesToReset() as $path) {
            file_put_contents($path, $this->fileContents[$path]);
        }
    }

    public function getFilesToReset(): array
    {
        return [
            testAppPath('.env.example'),
        ];
    }

    public function assertFileChanged(string $filePath, Closure $callback = null): self
    {
        if (!key_exists($filePath, $this->fileContents)) {
            throw new BadMethodCallException("The file [{$filePath}] was not configured as a resettable file.");
        }

        $callback ??= fn($newContent, $originalContent) => $newContent !== $originalContent;

        throw_unless(
            $callback(file_get_contents($filePath), $this->fileContents[$filePath]),
            new ExpectationFailedException("The contents of [{$filePath}] are unchanged."),
        );

        return $this;
    }

    public function assertFileNotChanged(string $filePath): self
    {
        try {
            $this->assertFileChanged($filePath);
        } catch (ExpectationFailedException $exception) {
            return $this;
        }

        throw new ExpectationFailedException("The contents of [{$filePath}] were updated.");
    }
}
