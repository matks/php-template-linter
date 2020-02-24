<?php

namespace Matks\PHPTemplateLinter;

class LineLinter
{
    const USECASE_NOTHING = 'nothing';
    const USECASE_OPENING = 'opening';
    const USECASE_CLOSING = 'closing';
    const USECASE_OPEN_AND_CLOSE = 'open-and-close';

    const OPERATION_IGNORED = 'ignored';
    const OPERATION_NOTHING = 'nothing';
    const OPERATION_IGNORED_BECAUSE_MULTILINE = 'ignored-multiline';
    const OPERATION_FIXED = 'fixed';

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * @var LineLinterConfigurationProcessor
     */
    private $configurationProcessor;

    /**
     * @param LineLinterConfiguration $configuration
     * @param bool $debug
     */
    public function __construct(LineLinterConfiguration $configuration, $debug = false)
    {
        $this->debug = $debug;

        $this->configurationProcessor = new LineLinterConfigurationProcessor();
        $this->configurationProcessor->loadConfiguration($configuration);
    }

    /**
     * @param LineLinterInput $input
     *
     * @return LineLinterResult
     */
    public function fixLineIndentation(LineLinterInput $input)
    {
        $line = $input->inputLine;
        $lineNumber = $input->lineNumber;
        $currentIndentationLevel = $input->currentIndentationLevel;
        $initialParsingStatus = $input->currentParsingStatus;
        $multiLineType = $input->multiLineType;

        $isOpeningLineLvl = 0;
        $isSpecialLineLvl = 0;
        $isClosingLineLvl = 0;

        // handle multi-line statements

        $weAreAlreadyInsideAMultilineStatement = ($initialParsingStatus > 0);

        if ($weAreAlreadyInsideAMultilineStatement) {

            if ($this->debug) {
                echo "Line $lineNumber - already inside multiline statement" . PHP_EOL;
            }

            $currentParsingStatus = $this->updateParsingStatus($line, $lineNumber, $initialParsingStatus);

            $isThisTheLastPartOfAMultilineStatement = ($currentParsingStatus === 0 && $initialParsingStatus > 0);

            if (false === $isThisTheLastPartOfAMultilineStatement) {
                if ($this->debug) {
                    echo "Line $lineNumber is ignored because we are inside multi-line statement" . PHP_EOL;
                }

                return new LineLinterResult(
                    $line,
                    $currentIndentationLevel,
                    $currentParsingStatus,
                    self::OPERATION_IGNORED_BECAUSE_MULTILINE,
                    $multiLineType
                );
            } else {
                switch ($multiLineType) {
                    case self::USECASE_OPEN_AND_CLOSE:
                        $isSpecialLineLvl++;
                        break;

                    case self::USECASE_OPENING:
                        $isSpecialLineLvl++;
                        break;

                    case self::USECASE_CLOSING:
                        $isClosingLineLvl++;
                        break;
                }

                if ($this->debug) {
                    echo "Line $lineNumber - ends multiline statement (type : $multiLineType)" . PHP_EOL;
                }
            }
        }

        $currentParsingStatus = $this->updateParsingStatus($line, $lineNumber, $initialParsingStatus);

        // handle ignored lines

        if ($this->shouldIgnoreThisLine($line, $lineNumber)) {
            return new LineLinterResult(
                $line,
                $currentIndentationLevel,
                $currentParsingStatus,
                self::OPERATION_IGNORED,
                $multiLineType
            );
        }

        // handle regular lines that are eligible to indentation

        $noSpaceLine = ltrim($line, " \t");

        $isOpeningLineLvl += $this->isOpeningLine($noSpaceLine, $lineNumber);
        $isSpecialLineLvl += $this->isSpecialLine($noSpaceLine, $lineNumber);
        $isClosingLineLvl += $this->isClosingLine($noSpaceLine, $lineNumber);

        $case = $this->findUsecase(
            $isOpeningLineLvl,
            $isSpecialLineLvl,
            $isClosingLineLvl
        );

        // handle multiline statement beginning

        $isFirstPartOfAMultilineStatement = ($currentParsingStatus > 0 && $initialParsingStatus === 0);

        if ($isFirstPartOfAMultilineStatement) {
            $multiLineType = $case;

            if ($this->debug) {
                echo "Line $lineNumber - starts multiline statement (type: $multiLineType)" . PHP_EOL;
            }


        }

        $indentedLine = null;

        switch ($case) {
            case self::USECASE_NOTHING:
                $indentedLine = $this->putXBlankSpace($currentIndentationLevel) . $noSpaceLine;
                $operationPerformed = self::OPERATION_NOTHING;
                break;

            case self::USECASE_OPEN_AND_CLOSE:
                $currentIndentationLevel = $this->decrementIndentationLevel($lineNumber, $currentIndentationLevel);
                $indentedLine = $this->putXBlankSpace($currentIndentationLevel) . $noSpaceLine;
                $currentIndentationLevel = $this->incrementIndentationLevel($lineNumber, $currentIndentationLevel);

                if ($indentedLine !== $line) {
                    $operationPerformed = self::OPERATION_FIXED;
                } else {
                    $operationPerformed = self::OPERATION_NOTHING;
                }
                break;

            case self::USECASE_OPENING:
                $indentedLine = $this->putXBlankSpace($currentIndentationLevel) . $noSpaceLine;
                $currentIndentationLevel = $this->incrementIndentationLevel($lineNumber, $currentIndentationLevel);

                if ($indentedLine !== $line) {
                    $operationPerformed = self::OPERATION_FIXED;
                } else {
                    $operationPerformed = self::OPERATION_NOTHING;
                }

                break;

            case self::USECASE_CLOSING:
                $currentIndentationLevel = $this->decrementIndentationLevel($lineNumber, $currentIndentationLevel);
                $indentedLine = $this->putXBlankSpace($currentIndentationLevel) . $noSpaceLine;

                if ($indentedLine !== $line) {
                    $operationPerformed = self::OPERATION_FIXED;
                } else {
                    $operationPerformed = self::OPERATION_NOTHING;
                }

                break;

            default:
                throw new \RuntimeException('Should never happen');
        }

        return new LineLinterResult(
            $indentedLine,
            $currentIndentationLevel,
            $currentParsingStatus,
            $operationPerformed,
            $multiLineType
        );
    }

    /**
     * @param int $openingLineLevel
     * @param int $openAndCloseLevel
     * @param int $closingLineLevel
     *
     * @return string
     */
    public function findUsecase($openingLineLevel, $openAndCloseLevel, $closingLineLevel)
    {
        if ($openingLineLevel == $closingLineLevel) {

            $noOpenAndNoClose = ($openingLineLevel === 0 && $closingLineLevel === 0);

            if ($noOpenAndNoClose && $openAndCloseLevel > 0) {
                return self::USECASE_OPEN_AND_CLOSE;
            }

            return self::USECASE_NOTHING;
        }

        if ($openingLineLevel > $closingLineLevel) {
            return self::USECASE_OPENING;
        }

        if ($closingLineLevel > $openingLineLevel) {
            return self::USECASE_CLOSING;
        }

        if ($openAndCloseLevel > 0) {
            return self::USECASE_OPEN_AND_CLOSE;
        }

        return self::USECASE_NOTHING;
    }

    /**
     * Attempts to find whether we are inside a multi-line statements like
     * <a href="..."
     *    class="..."
     *    style="..."
     * >
     *
     * @param string $line
     * @param int $lineNumber
     * @param int $currentParsingStatus
     *
     * @return int
     */
    public function updateParsingStatus($line, $lineNumber, $currentParsingStatus)
    {
        $containsOpening = ['<', '(', '{', '['];
        $containsClosing = ['>', ')', '}', ']'];

        $countOpening = 0;
        $countClosing = 0;

        foreach ($containsOpening as $contain) {
            $countOpening += substr_count($line, $contain);
        }
        foreach ($containsClosing as $contain) {
            $countClosing += substr_count($line, $contain);
        }

        $newCurrentParsingStatus = $currentParsingStatus + ($countOpening - $countClosing);
        if ($currentParsingStatus !== $newCurrentParsingStatus) {
            $currentParsingStatus = $newCurrentParsingStatus;

            if ($this->debug) {
                echo "line $lineNumber - parsing status updated to " . $currentParsingStatus . PHP_EOL;
            }
        }

        return $currentParsingStatus;
    }

    public function shouldIgnoreThisLine($line, $lineNumber)
    {
        $result = $this->findHowManyOccurrences(
            $line,
            $this->configurationProcessor->getIgnoreChars()
        );

        if ($result > 0 && $this->debug) {
            echo "Line $lineNumber is ignored" . PHP_EOL;
        }

        return ($result > 0);
    }

    public function isOpeningLine($line, $lineNumber)
    {
        $result = $this->findHowManyOccurrences(
            $line,
            $this->configurationProcessor->getOpeningChars()
        );

        if ($result > 0 && $this->debug) {
            echo "Line $lineNumber is opening line" . PHP_EOL;
        }

        return $result;
    }

    public function isSpecialLine($line, $lineNumber)
    {
        $result = $this->findHowManyOccurrences(
            $line,
            $this->configurationProcessor->getOpenAndCloseChars()
        );

        if ($result > 0 && $this->debug) {
            echo "Line $lineNumber is opening and closing line" . PHP_EOL;
        }

        return $result;
    }

    public function isClosingLine($line, $lineNumber)
    {
        $result = $this->findHowManyOccurrences(
            $line,
            $this->configurationProcessor->getClosingChars()
        );

        if ($result > 0 && $this->debug) {
            echo "Line $lineNumber is closing line" . PHP_EOL;
        }

        return $result;
    }

    /**
     * @param string $line
     * @param array $contains
     *
     * @return int
     */
    public function findHowManyOccurrences($line, $contains = [])
    {
        $level = 0;

        foreach ($contains as $contain) {
            $level += substr_count($line, $contain);
        }

        return $level;
    }

    /**
     * @param int $number
     *
     * @return string
     */
    public function putXBlankSpace($number)
    {
        $result = str_repeat(" ", $number);

        return $result;
    }

    /**
     * @param int $lineNumber
     * @param int $currentIndentationLevel
     *
     * @return int
     */
    public function incrementIndentationLevel($lineNumber, $currentIndentationLevel)
    {
        $currentIndentationLevel += $this->configurationProcessor->getIndentationLevel();

        if ($this->debug) {
            echo "Line $lineNumber - indentation level raised to " . $currentIndentationLevel . PHP_EOL;
        }

        return $currentIndentationLevel;
    }

    /**
     * @param int $lineNumber
     * @param int $currentIndentationLevel
     *
     * @return int
     */
    public function decrementIndentationLevel($lineNumber, $currentIndentationLevel)
    {
        $currentIndentationLevel -= $this->configurationProcessor->getIndentationLevel();

        if ($currentIndentationLevel < 0) {
            $currentIndentationLevel = 0;
        }

        if ($this->debug) {
            echo "Line $lineNumber - indentation level decreased to " . $currentIndentationLevel . PHP_EOL;
        }

        return $currentIndentationLevel;
    }
}
