<?php

namespace Matks\PHPTemplateLinter;

class LinterManager
{
    const TYPE_SMARTY = 'smarty';
    const TYPE_TWIG = 'twig';

    private $numberOfSpaces = 4;
    private $debug = false;
    private $latestReport = null;

    /**
     * @var LineLinter
     */
    private $lineLinter;

    public function __construct()
    {
        $defaultConfig = new DefaultConfiguration();
        $this->lineLinter = new LineLinter($defaultConfig->get());
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

        $handle = fopen($filepath, 'r+');
        $result = '';
        $currentIndentationLevel = 0;
        $currentParsingStatus = 0;
        $lineNumber = 1;

        $i = 0;
        $report = [];

        if ($handle) {
            while (($line = fgets($handle)) !== false) {

                $input = new LineLinterInput($lineNumber, $line, $currentIndentationLevel, $currentParsingStatus);

                $lintResult = $this->lineLinter->fixLineIndentation($input);
                $lintedLine = $lintResult->lintedLine;
                $currentParsingStatus = $lintResult->updatedParsingStatus;
                $currentIndentationLevel = $lintResult->updatedIndentationLevel;

                $report[$i] = $lintResult->operationPerformed;

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
            throw new \RuntimeException('Failed to read file ' . $filepath);
        }

        echo "Parsed $i lines" . PHP_EOL;

        $this->latestReport = $report;

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

    /**
     * @return null|string[]
     */
    public function getLatestReport()
    {
        return $this->latestReport;
    }
}
