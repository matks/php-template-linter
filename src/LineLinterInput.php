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
    /** @var string */
    public $multiLineType;

    /**
     * @param int $lineNumber
     * @param string $inputLine
     * @param int $currentIndentationLevel
     * @param int $currentParsingStatus
     */
    public function __construct($lineNumber, $inputLine, $currentIndentationLevel, $currentParsingStatus, $multiLineType = null)
    {
        $this->lineNumber = $lineNumber;
        $this->inputLine = $inputLine;
        $this->currentIndentationLevel = $currentIndentationLevel;
        $this->currentParsingStatus = $currentParsingStatus;
        $this->multiLineType = $multiLineType;
    }
}
