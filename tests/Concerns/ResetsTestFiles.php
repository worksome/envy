<?php

namespace Worksome\Envy\Tests\Concerns;

use BadMethodCallException;
use Closure;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;
use Throwable;

use function Safe\copy;
use function Safe\file_get_contents;
use function Safe\unlink;

/**
 * @mixin TestCase
 */
trait ResetsTestFiles
{
    private array $filesToReset = [];
    private array $fileContents = [];

    public function setUpResetsTestFiles(): void
    {
        if (! in_array('withoutPublishedConfigFile', $this->getGroups())) {
            // We always want a fresh copy of the envy config file.
            copy(__DIR__ . '/../../config/envy.php', testAppPath('config/envy.php'));
        }

        foreach ($this->getFilesToReset() as $path) {
            try {
                $this->fileContents[$path] = file_get_contents($path);
            } catch (Throwable) {
            }
        }
    }

    public function tearDownResetsTestFiles(): void
    {
        foreach ($this->getFilesToReset() as $path) {
            try {
                file_put_contents($path, $this->fileContents[$path]);
            } catch (Throwable) {
            }
        }

        if (! in_array('withoutPublishedConfigFile', $this->getGroups())) {
            unlink(testAppPath('config/envy.php'));
        }
    }

    public function addResettableFile(string $path): self
    {
        $this->filesToReset[] = $path;
        $this->fileContents[$path] = file_get_contents($path);

        return $this;
    }

    public function getFilesToReset(): array
    {
        return [
            ...$this->filesToReset,
            testAppPath('.env.example'),
            testAppPath('config/envy.php'),
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
            new ExpectationFailedException(
                "The contents of [{$filePath}] are unchanged.",
                $this->buildComparisonFailure($filePath)
            ),
        );

        $this->addToAssertionCount(1);

        return $this;
    }

    public function assertFileNotChanged(string $filePath): self
    {
        try {
            $this->assertFileChanged($filePath);
        } catch (ExpectationFailedException $exception) {
            $this->addToAssertionCount(1);

            return $this;
        }

        throw new ExpectationFailedException(
            "The contents of [{$filePath}] were updated.",
            $this->buildComparisonFailure($filePath)
        );
    }

    private function buildComparisonFailure(string $filePath): ComparisonFailure
    {
        $fileContents = file_get_contents($filePath);

        return new ComparisonFailure(
            $this->fileContents[$filePath],
            $fileContents,
            $this->fileContents[$filePath],
            $fileContents,
        );
    }
}
