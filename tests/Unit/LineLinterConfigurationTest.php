<?php

namespace MatksTests\Unit;

use Matks\PHPTemplateLinter\DefaultConfiguration;
use Matks\PHPTemplateLinter\LineLinter;
use Matks\PHPTemplateLinter\LineLinterConfiguration;
use Matks\PHPTemplateLinter\LineLinterConfigurationItem;
use Matks\PHPTemplateLinter\LineLinterInput;
use Matks\PHPTemplateLinter\LineLinterResult;
use PHPUnit\Framework\TestCase;

class LineLinterConfigurationTest extends TestCase
{
    public function testConfigurationOne()
    {
        $configuration = $this->buildConfiguration();

        $linter = new LineLinter($configuration, false);

        $line = 'b';
        $input = new LineLinterInput(1, $line, 2, 0);
        $result = $linter->fixLineIndentation($input);

        $this->assertInstanceOf(LineLinterResult::class, $result);
        $this->assertEquals(LineLinter::OPERATION_FIXED, $result->operationPerformed);
        $this->assertEquals('  b', $result->lintedLine);
    }

    public function testConfigurationTwo()
    {
        $configuration = $this->buildConfiguration();

        $linter = new LineLinter($configuration, false);

        $line = '  c';
        $input = new LineLinterInput(1, $line, 2, 0);
        $result = $linter->fixLineIndentation($input);

        $this->assertInstanceOf(LineLinterResult::class, $result);
        $this->assertEquals(LineLinter::OPERATION_FIXED, $result->operationPerformed);
        $this->assertEquals('c', $result->lintedLine);
    }

    public function testConfigurationThree()
    {
        $configuration = $this->buildConfiguration();

        $linter = new LineLinter($configuration, false);

        $line = 'a';
        $input = new LineLinterInput(1, $line, 2, 0);
        $result = $linter->fixLineIndentation($input);

        $this->assertInstanceOf(LineLinterResult::class, $result);
        $this->assertEquals(LineLinter::OPERATION_IGNORED, $result->operationPerformed);
        $this->assertEquals('a', $result->lintedLine);
    }

    /**
     * @return LineLinterConfiguration
     */
    private function buildConfiguration()
    {
        $configuration = [
            LineLinterConfigurationItem::TYPE_IGNORE_CHAR => ['a'],
            LineLinterConfigurationItem::TYPE_OPENING_CHAR => ['b'],
            LineLinterConfigurationItem::TYPE_OPEN_AND_CLOSE_CHAR => ['c'],
            LineLinterConfigurationItem::TYPE_CLOSING_CHAR => ['d'],
        ];

        return LineLinterConfiguration::fromArray($configuration, 2);
    }
}
