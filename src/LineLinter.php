<?php

class LineLinter
{
    const USECASE_NOTHING = 'nothing';
    const USECASE_OPENING = 'opening';
    const USECASE_CLOSING = 'closing';
    const USECASE_OPEN_AND_CLOSE = 'open-and-close';

    private $debug = false;
    private $numberOfSpaces = 2;

    /**
     * @param int $numberOfSpaces
     */
    public function setNumberOfSpaces($numberOfSpaces)
    {
        $this->numberOfSpaces = $numberOfSpaces;
    }

    public function fixLineIndentation(LineLinterInput $input)
    {
        $line = $input->inputLine;
        $lineNumber = $input->lineNumber;
        $currentIndentationLevel = $input->currentIndentationLevel;
        $currentParsingStatus = $input->currentParsingStatus;

        if ($this->shouldIgnoreThisLine($line, $lineNumber, $currentParsingStatus)) {
            $currentParsingStatus = $this->updateParsingStatus($line, $lineNumber, $currentParsingStatus);

            return new LineLinterResult($line, $currentIndentationLevel, $currentParsingStatus);
        }

        $noSpaceLine = ltrim($line, " \t");

        $isOpeningLineLvl = $this->isOpeningLine($noSpaceLine, $lineNumber);
        $isSpecialLineLvl = $this->isSpecialLine($noSpaceLine, $lineNumber);
        $isClosingLineLvl = $this->isClosingLine($noSpaceLine, $lineNumber);

        $case = $this->findUsecase(
            $isOpeningLineLvl,
            $isSpecialLineLvl,
            $isClosingLineLvl
        );

        $indentedLine = null;

        switch ($case) {
            case self::USECASE_NOTHING:
                $indentedLine = $this->putXBlankSpace($currentIndentationLevel) . $noSpaceLine;
                break;

            case self::USECASE_OPEN_AND_CLOSE:
                $currentIndentationLevel = $this->decrementIndentationLevel($currentIndentationLevel);
                $indentedLine = $this->putXBlankSpace($currentIndentationLevel) . $noSpaceLine;
                $currentIndentationLevel = $this->incrementIndentationLevel($currentIndentationLevel);
                break;

            case self::USECASE_OPENING:
                $indentedLine = $this->putXBlankSpace($currentIndentationLevel) . $noSpaceLine;
                $currentIndentationLevel = $this->incrementIndentationLevel($currentIndentationLevel);
                break;

            case self::USECASE_CLOSING:
                $currentIndentationLevel = $this->decrementIndentationLevel($currentIndentationLevel);
                $indentedLine = $this->putXBlankSpace($currentIndentationLevel) . $noSpaceLine;
                break;

            default:
                throw new \RuntimeException('Should never happen');
        }

        $currentParsingStatus = $this->updateParsingStatus($noSpaceLine, $lineNumber, $currentParsingStatus);

        return new LineLinterResult($indentedLine, $currentIndentationLevel, $currentParsingStatus);
    }

    /**
     * @param int $openingLineLevel
     * @param int $openAndCloseLevel
     * @param int $closingLineLevel
     *
     * @return string
     */
    private function findUsecase($openingLineLevel, $openAndCloseLevel, $closingLineLevel)
    {
        if ($openAndCloseLevel > 0) {
            return self::USECASE_OPEN_AND_CLOSE;
        }

        if ($openingLineLevel == $closingLineLevel) {
            return self::USECASE_NOTHING;
        }

        if ($openingLineLevel > $closingLineLevel) {
            return self::USECASE_OPENING;
        }

        if ($closingLineLevel > $openingLineLevel) {
            return self::USECASE_CLOSING;
        }

        return self::USECASE_NOTHING;
    }

    /**
     * Attempts to find whether we are inside a multi-line statements like
     * <a href="..."
     *    class="..."
     *    style="..."
     * >
     */
    private function updateParsingStatus($line, $lineNumber, $currentParsingStatus)
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
                echo "Parsing status updated at line $lineNumber: " . $currentParsingStatus . PHP_EOL;
            }
        }

        return $currentParsingStatus;
    }

    private function shouldIgnoreThisLine($line, $lineNumber, $currentParsingStatus)
    {
        if ($currentParsingStatus > 0) {
            if ($this->debug) {
                echo "Line $lineNumber is ignored because we are inside multi-line statement" . PHP_EOL;
            }

            return true;
        }

        $result = $this->findHowManyOccurrences(
            $line,
            ['{#', '*']
        );

        if ($result > 0 && $this->debug) {
            echo "Line $lineNumber is ignored" . PHP_EOL;
        }

        return ($result > 0);
    }

    private function isOpeningLine($line, $lineNumber)
    {
        $result = $this->findHowManyOccurrences(
            $line,
            [
                '{% block', '{% if', '{% for',
                '<div',
                '<h', '<i', '<p', '<a',
                '<thead', '<td', '<tr', '<th', '<table', '<tbody',
                '<span', '<button',
            ]
        );

        if ($result > 0 && $this->debug) {
            echo "Line $lineNumber is opening line" . PHP_EOL;
        }

        return $result;
    }

    private function isSpecialLine($line, $lineNumber)
    {
        $result = $this->findHowManyOccurrences(
            $line,
            ['{% else']
        );

        if ($result > 0 && $this->debug) {
            echo "Line $lineNumber is opening and closing line" . PHP_EOL;
        }

        return $result;
    }

    private function isClosingLine($line, $lineNumber)
    {
        $result = $this->findHowManyOccurrences(
            $line,
            [
                '{% endblock', '{% endif', '{% endfor',
                '</div',
                '</h', '</i', '</p', '</a',
                '</thead', '</td', '</tr', '</th', '</table', '</tbody',
                '</span', '</button'
            ]
        );

        if ($result > 0 && $this->debug) {
            echo "Line $lineNumber is closing line" . PHP_EOL;
        }

        return $result;
    }

    /**
     * @param $line
     * @param array $contains
     *
     * @return int
     */
    private function findHowManyOccurrences($line, $contains = [])
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
    private function putXBlankSpace($number)
    {
        $result = str_repeat(" ", $number);

        return $result;
    }

    private function incrementIndentationLevel($currentIndentationLevel)
    {
        $currentIndentationLevel += $this->numberOfSpaces;

        if ($this->debug) {
            echo "Indentation level raised to " . $currentIndentationLevel . PHP_EOL;
        }

        return $currentIndentationLevel;
    }

    private function decrementIndentationLevel($currentIndentationLevel)
    {
        $currentIndentationLevel -= $this->numberOfSpaces;

        if ($currentIndentationLevel < 0) {
            $currentIndentationLevel = 0;
        }

        if ($this->debug) {
            echo "Indentation level decreased to " . $currentIndentationLevel . PHP_EOL;
        }

        return $currentIndentationLevel;
    }
}
