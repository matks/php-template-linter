<?php

namespace MatksTests\Unit;

use Matks\PHPTemplateLinter\LineLinter;
use PHPUnit\Framework\TestCase;

class LineLinterFindOccurrencesTest extends TestCase
{
    public function testFindOccurrencesZero()
    {
        $linter = new LineLinter(false);

        $line = '<a href="aaa">';
        $result = $linter->updateParsingStatus($line, 1, 0);

        $this->assertEquals(0, $result);
    }

    public function testUpdateParsingStatusSimple()
    {
        $linter = new LineLinter(false);

        $line = '<a href="aaa"';
        $result = $linter->updateParsingStatus($line, 1, 0);

        $this->assertEquals(1, $result);
    }

    public function testUpdateParsingStatusDouble()
    {
        $linter = new LineLinter(false);

        $line = '<a href="aaa" {% if';
        $result = $linter->updateParsingStatus($line, 1, 0);

        $this->assertEquals(2, $result);
    }
}
