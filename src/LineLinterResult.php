<?php

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

    /**
     * @param string $lintedLine
     * @param int $updatedIndentationLevel
     * @param int $updatedParsingStatus
     * @param string $operationPerformed
     */
    public function __construct($lintedLine, $updatedIndentationLevel, $updatedParsingStatus, $operationPerformed)
    {
        $this->lintedLine = $lintedLine;
        $this->updatedIndentationLevel = $updatedIndentationLevel;
        $this->updatedParsingStatus = $updatedParsingStatus;
        $this->operationPerformed = $operationPerformed;
    }
}
