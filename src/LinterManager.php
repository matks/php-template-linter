<?php

class LinterManager
{
    const TYPE_SMARTY = 'smarty';
    const TYPE_TWIG = 'twig';

    private $numberOfSpaces = 4;
    private $debug = false;

    /**
     * @var LineLinter
     */
    private $lineLinter;

    public function __construct()
    {
        $this->lineLinter = new LineLinter();
    }

    /**
     * @param string $filepath
     * @param string $type
     * @param bool $dryRun
     *
     * @return string
     */
    public function lintFile($filepath, $type, $dryRun = false)
    {
        if (!file_exists($filepath)) {
            throw new \InvalidArgumentException('No file at ' . $filepath);
        }

        if (!in_array($type, [self::TYPE_SMARTY, self::TYPE_TWIG])) {
            throw new \InvalidArgumentException('Cannot handle file type ' . $type);
        }

        if ($type === self::TYPE_TWIG) {
            $this->numberOfSpaces = 2;
        } else if ($type === self::TYPE_SMARTY) {
            $this->numberOfSpaces = 2;
        }

        $this->lineLinter->setNumberOfSpaces($this->numberOfSpaces);

        $handle = fopen($filepath, 'r+');
        $result = '';
        $currentIndentationLevel = 0;
        $currentParsingStatus = 0;
        $lineNumber = 1;

        $i = 0;

        if ($handle) {
            // @todo: it skips last line if last line is empty !!!
            while (($line = fgets($handle)) !== false) {

                $input = new LineLinterInput($lineNumber, $line, $currentIndentationLevel, $currentParsingStatus);

                $lintResult = $this->lineLinter->fixLineIndentation($input);
                $lintedLine = $lintResult->lintedLine;
                $currentParsingStatus = $lintResult->updatedParsingStatus;
                $currentIndentationLevel = $lintResult->updatedIndentationLevel;

                $i++;

                if ($lintedLine != '') {
                    $result .= $lintedLine;
                } else {
                    $result .= PHP_EOL;
                }

                $lineNumber++;
            }


            fclose($handle);
        } else {
            throw new \RuntimeException('Failed to read file '.$filepath);
        }

        echo "Parsed $i lines" . PHP_EOL;

        return $result;
    }

    public function enableDebug()
    {
        $this->debug = true;
    }

    public function disableDebug()
    {
        $this->debug = false;
    }


}
