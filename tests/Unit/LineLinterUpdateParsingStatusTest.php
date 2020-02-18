<?php

namespace MatksTests\Unit;

use Matks\PHPTemplateLinter\LineLinter;
use PHPUnit\Framework\TestCase;

class LineLinterUpdateParsingStatusTest extends TestCase
{
    public function testUpdateParsingStatusNone()
    {
        $linter = new LineLinter(false);

        $line = '<div href="bbb">';
        $result = $linter->findHowManyOccurrences($line, ['a']);

        $this->assertEquals(0, $result);
    }

    public function testUpdateParsingStatusOne()
    {
        $linter = new LineLinter(false);

        $line = '<a href="s">';
        $result = $linter->findHowManyOccurrences($line, ['a']);

        $this->assertEquals(1, $result);
    }

    public function testUpdateParsingStatusTwo()
    {
        $linter = new LineLinter(false);

        $line = '<a href="aff">';
        $result = $linter->findHowManyOccurrences($line, ['a']);

        $this->assertEquals(2, $result);
    }
}
