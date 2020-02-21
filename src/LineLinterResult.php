<?php

namespace Matks\PHPTemplateLinter;

class LineLinterResult
{
    /** @var string */
    public $lintedLine;
    /** @var int */
    public $updatedIndentationLevel;
    /** @var int */
    public $updatedParsingStatus;
    /** @var string */
    public $operationPerformed;
    /** @var string */
    public $multiLineType;

    /**
     * @param string $lintedLine
     * @param int $updatedIndentationLevel
     * @param int $updatedParsingStatus
     * @param string $operationPerformed
     */
    public function __construct($lintedLine, $updatedIndentationLevel, $updatedParsingStatus, $operationPerformed, $multiLineType = null)
    {
        $this->lintedLine = $lintedLine;
        $this->updatedIndentationLevel = $updatedIndentationLevel;
        $this->updatedParsingStatus = $updatedParsingStatus;
        $this->operationPerformed = $operationPerformed;
        $this->multiLineType = $multiLineType;
    }
}
