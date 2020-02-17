<?php

class LineLinterResult
{
    /** @var string */
    public $lintedLine;
    /** @var int */
    public $updatedIndentationLevel;
    /** @var int */
    public $updatedParsingStatus;

    /**
     * @param string $lintedLine
     * @param int $updatedIndentationLevel
     * @param int $updatedParsingStatus
     */
    public function __construct($lintedLine, $updatedIndentationLevel, $updatedParsingStatus)
    {
        $this->lintedLine = $lintedLine;
        $this->updatedIndentationLevel = $updatedIndentationLevel;
        $this->updatedParsingStatus = $updatedParsingStatus;
    }
}
