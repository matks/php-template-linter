<?php

namespace MatksTests\Unit;

use Matks\PHPTemplateLinter\DefaultConfiguration;
use Matks\PHPTemplateLinter\LineLinter;
use Matks\PHPTemplateLinter\LineLinterInput;
use MatksTests\TestUtils;
use PHPUnit\Framework\TestCase;

class LineLinterMultilineStatementTest extends TestCase
{
    /**
     * Increment first and last part of the multi-line statement, ignores the middle part
     */
    public function testMultilineBasic()
    {
        $linter = new LineLinter(DefaultConfiguration::get(), false);

        $statement =
            '<div style="aaa"
  class="bbb"
  data-id="3"
>';

        $expectedLines = [
            '  <div style="aaa"',
            '  class="bbb"',
            '  data-id="3"',
            '  >'
        ];

        $lines = TestUtils::splitStringIntoArray($statement);

        $lineInput1 = new LineLinterInput(1, $lines[0], 2, 0);
        $lintedResultLine1 = $linter->fixLineIndentation($lineInput1);

        $this->assertEquals(LineLinter::OPERATION_FIXED, $lintedResultLine1->operationPerformed);
        $this->assertEquals(4, $lintedResultLine1->updatedIndentationLevel);
        $this->assertEquals(1, $lintedResultLine1->updatedParsingStatus);
        $this->assertEquals($expectedLines[0], $lintedResultLine1->lintedLine);
        $this->assertEquals(LineLinter::USECASE_OPENING, $lintedResultLine1->multiLineType);

        $lineInput2 = new LineLinterInput(2, $lines[1], $lintedResultLine1->updatedIndentationLevel, 1);
        $lintedResultLine2 = $linter->fixLineIndentation($lineInput2);

        $this->assertEquals(LineLinter::OPERATION_IGNORED_BECAUSE_MULTILINE, $lintedResultLine2->operationPerformed);
        $this->assertEquals(4, $lintedResultLine2->updatedIndentationLevel);
        $this->assertEquals(1, $lintedResultLine2->updatedParsingStatus);
        $this->assertEquals($expectedLines[1], $lintedResultLine2->lintedLine);

        $lineInput3 = new LineLinterInput(3, $lines[2], $lintedResultLine2->updatedIndentationLevel, 1);
        $lintedResultLine3 = $linter->fixLineIndentation($lineInput3);

        $this->assertEquals(LineLinter::OPERATION_IGNORED_BECAUSE_MULTILINE, $lintedResultLine3->operationPerformed);
        $this->assertEquals(4, $lintedResultLine3->updatedIndentationLevel);
        $this->assertEquals(1, $lintedResultLine3->updatedParsingStatus);
        $this->assertEquals($expectedLines[2], $lintedResultLine3->lintedLine);

        $lineInput4 = new LineLinterInput(4, $lines[3], $lintedResultLine3->updatedIndentationLevel, 1, $lintedResultLine1->multiLineType);
        $lintedResultLine4 = $linter->fixLineIndentation($lineInput4);

        $this->assertEquals(LineLinter::OPERATION_FIXED, $lintedResultLine4->operationPerformed);
        $this->assertEquals(4, $lintedResultLine4->updatedIndentationLevel);
        $this->assertEquals(0, $lintedResultLine4->updatedParsingStatus);
        $this->assertEquals($expectedLines[3], $lintedResultLine4->lintedLine);
    }
}
