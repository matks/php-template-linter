<?php

namespace MatksTests\Unit;

use Matks\PHPTemplateLinter\LineLinter;
use Matks\PHPTemplateLinter\LineLinterInput;
use Matks\PHPTemplateLinter\LineLinterResult;
use PHPUnit\Framework\TestCase;

class LineLinterFixIndentationTest extends TestCase
{
    public function testNothingToFix()
    {
        $linter = new LineLinter(false);

        $line = '    <div>';
        $input = new LineLinterInput(1, $line, 4, 0);
        $result = $linter->fixLineIndentation($input);

        $this->assertInstanceOf(LineLinterResult::class, $result);
        $this->assertEquals(LineLinter::OPERATION_NOTHING, $result->operationPerformed);
        $this->assertEquals('    <div>', $result->lintedLine);
    }

    public function testFixLineOne()
    {
        $linter = new LineLinter(false);

        $line = '    <div>';
        $input = new LineLinterInput(1, $line, 2, 0);
        $result = $linter->fixLineIndentation($input);

        $this->assertInstanceOf(LineLinterResult::class, $result);
        $this->assertEquals(LineLinter::OPERATION_FIXED, $result->operationPerformed);
        $this->assertEquals('  <div>', $result->lintedLine);
    }

    public function testFixLineTwo()
    {
        $linter = new LineLinter(false);

        $line = '{% else %}';
        $input = new LineLinterInput(1, $line, 4, 0);
        $result = $linter->fixLineIndentation($input);

        $this->assertInstanceOf(LineLinterResult::class, $result);
        $this->assertEquals(LineLinter::OPERATION_FIXED, $result->operationPerformed);
        $this->assertEquals('  {% else %}', $result->lintedLine);
    }

    public function testFixLineThree()
    {
        $linter = new LineLinter(false);

        $line = '    <div>';
        $input = new LineLinterInput(1, $line, 6, 0);
        $result = $linter->fixLineIndentation($input);

        $this->assertInstanceOf(LineLinterResult::class, $result);
        $this->assertEquals(LineLinter::OPERATION_FIXED, $result->operationPerformed);
        $this->assertEquals('      <div>', $result->lintedLine);
    }

    public function testFixLineMultiLine()
    {
        $linter = new LineLinter(false);

        $line = '    <a href="aaa">';
        $input = new LineLinterInput(1, $line, 6, 2);
        $result = $linter->fixLineIndentation($input);

        $this->assertInstanceOf(LineLinterResult::class, $result);
        $this->assertEquals(LineLinter::OPERATION_IGNORED_BECAUSE_MULTILINE, $result->operationPerformed);
        $this->assertEquals('    <a href="aaa">', $result->lintedLine);
    }
}
