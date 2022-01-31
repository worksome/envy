<?php

namespace Worksome\Envsync\Tests\Unit;

use Worksome\Envsync\Tests\Concerns\ResetsTestFiles;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use ResetsTestFiles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpResetsTestFiles();
    }

    protected function tearDown(): void
    {
        $this->tearDownResetsTestFiles();
        parent::tearDown();
    }
}
