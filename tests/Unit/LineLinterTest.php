<?php

namespace MatksTests\Unit;

use Matks\PHPTemplateLinter\LineLinter;
use PHPUnit\Framework\TestCase;

class LineLinterTest extends TestCase
{
    public function testUpdateParsingStatusSimple()
    {
        $linter = new LineLinter(false);

        $line = '<a href="aaa"';
        $result = $linter->updateParsingStatus($line, 1, 0);

        $this->assertEquals(1, $result);
    }
}
