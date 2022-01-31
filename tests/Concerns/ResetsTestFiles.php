<?php

namespace Worksome\Envsync\Tests\Concerns;

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
}
