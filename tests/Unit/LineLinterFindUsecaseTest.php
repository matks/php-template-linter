<?php

namespace MatksTests\Unit;

use Matks\PHPTemplateLinter\DefaultConfiguration;
use Matks\PHPTemplateLinter\LineLinter;
use PHPUnit\Framework\TestCase;

class LineLinterFindUsecaseTest extends TestCase
{
    public function testFindUsecaseOpening()
    {
        $linter = new LineLinter(DefaultConfiguration::get(), false);

        $result = $linter->findUsecase(1, 0, 0);

        $this->assertEquals(LineLinter::USECASE_OPENING, $result);
    }

    public function testFindUsecaseClosing()
    {
        $linter = new LineLinter(DefaultConfiguration::get(), false);

        $result = $linter->findUsecase(0, 0, 1);

        $this->assertEquals(LineLinter::USECASE_CLOSING, $result);
    }

    public function testFindUsecaseOpenAndClose()
    {
        $linter = new LineLinter(DefaultConfiguration::get(), false);

        $result = $linter->findUsecase(0, 1, 0);

        $this->assertEquals(LineLinter::USECASE_OPEN_AND_CLOSE, $result);
    }

    public function testFindUsecaseComplexOpening()
    {
        $linter = new LineLinter(DefaultConfiguration::get(), false);

        $result = $linter->findUsecase(4, 1, 3);

        $this->assertEquals(LineLinter::USECASE_OPENING, $result);
    }

    public function testFindUsecaseComplexClosing()
    {
        $linter = new LineLinter(DefaultConfiguration::get(), false);

        $result = $linter->findUsecase(4, 9, 11);

        $this->assertEquals(LineLinter::USECASE_CLOSING, $result);
    }

    public function testFindUsecaseComplexNothing()
    {
        $linter = new LineLinter(DefaultConfiguration::get(), false);

        $result = $linter->findUsecase(10, 1, 10);

        $this->assertEquals(LineLinter::USECASE_NOTHING, $result);
    }
}
