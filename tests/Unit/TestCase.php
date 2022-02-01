<?php

namespace Worksome\Envy\Tests\Unit;

use Worksome\Envy\Tests\Concerns\ResetsTestFiles;

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
