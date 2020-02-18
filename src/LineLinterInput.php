<?php

namespace Matks\PHPTemplateLinter;

class LineLinterInput
{
    /** @var int */
    public $lineNumber;
    /** @var string */
    public $inputLine;
    /** @var int */
    public $currentIndentationLevel;
    /** @var int */
    public $currentParsingStatus;

    /**
     * @param int $lineNumber
     * @param string $inputLine
     * @param int $currentIndentationLevel
     * @param int $currentParsingStatus
     */
    public function __construct($lineNumber, $inputLine, $currentIndentationLevel, $currentParsingStatus)
    {
        $this->lineNumber = $lineNumber;
        $this->inputLine = $inputLine;
        $this->currentIndentationLevel = $currentIndentationLevel;
        $this->currentParsingStatus = $currentParsingStatus;
    }
}
