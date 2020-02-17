<?php

class Linter
{
    private $currentIndentationLevel = 0;
    private $currentParsingStatus = 0;
    private $numberOfSpaces = 4;
    private $debug = true;

    public function getLintedFileContent($filepath, $type)
    {
        if ($type === 'twig') {
            $this->numberOfSpaces = 2;
        }

        $handle = fopen($filepath, 'r+');
        $result = '';
        $this->currentIndentationLevel = 0;
        $lineNumber = 1;

        $i = 0;

        if ($handle) {
            // @todo: it skips last line if last line is empty !!!
            while (($line = fgets($handle)) !== false) {

                $lintedLine = $this->processLine($line, $lineNumber);
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
            // error opening the file.
        }

        echo "Parsed $i lines" . PHP_EOL;

        return $result;
    }

    /**
     * @return int
     */
    public function getCurrentIndentationLevel()
    {
        return $this->currentIndentationLevel;
    }

    private function processLine($line, $lineNumber)
    {
        if ($this->shouldIgnoreThisLine($line, $lineNumber) > 0) {
            $this->updateParsingStatus($line, $lineNumber);
            return $line;
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
            case 'nothing':
                $indentedLine = $this->putXBlankSpace($this->currentIndentationLevel) . $noSpaceLine;
                break;

            case 'special':
                $this->decrementIndentationLevel();
                $indentedLine = $this->putXBlankSpace($this->currentIndentationLevel) . $noSpaceLine;
                $this->incrementIndentationLevel();
                break;

            case 'opening':
                $indentedLine = $this->putXBlankSpace($this->currentIndentationLevel) . $noSpaceLine;
                $this->incrementIndentationLevel();
                break;

            case 'closing':
                $this->decrementIndentationLevel();
                $indentedLine = $this->putXBlankSpace($this->currentIndentationLevel) . $noSpaceLine;
                break;

            default:
                throw new \RuntimeException('Should never happen');
        }

        $this->updateParsingStatus($noSpaceLine, $lineNumber);

        return $indentedLine;
    }

    private function findUsecase($isOpeningLine, $isSpecialLine, $isClosingLine)
    {
        if ($isSpecialLine > 0) {
            return 'special';
        }

        if ($isOpeningLine == $isClosingLine) {
            return 'nothing';
        }

        if ($isOpeningLine > $isClosingLine) {
            return 'opening';
        }

        if ($isClosingLine > $isOpeningLine) {
            return 'closing';
        }

        return 'nothing';
    }

    /**
     * Attempts to find whether we are inside a multi-line statements like
     * <a href="..."
     *    class="..."
     *    style="..."
     * >
     */
    private function updateParsingStatus($line, $lineNumber)
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

        $newCurrentParsingStatus = $this->currentParsingStatus + ($countOpening - $countClosing);
        if ($this->currentParsingStatus !== $newCurrentParsingStatus) {
            $this->currentParsingStatus = $newCurrentParsingStatus;

            if ($this->debug) {
                echo "Parsing status updated at line $lineNumber: " . $this->currentParsingStatus . PHP_EOL;
            }
        }

    }

    private function shouldIgnoreThisLine($line, $lineNumber)
    {
        if ($this->currentParsingStatus > 0) {
            echo "Line $lineNumber is ignored because we are inside multi-line statement" . PHP_EOL;
            return true;
        }

        $result = $this->doesLineMatches(
            $line,
            [
                '{#',
                '*'
            ],
            []
        );

        if ($result > 0 && $this->debug) {
            echo "Line $lineNumber is ignored" . PHP_EOL;
        }

        return $result;
    }

    private function isOpeningLine($line, $lineNumber)
    {
        $result = $this->doesLineMatches(
            $line,
            [
                '{% block', '{% if', '{% for',
                '<div',
                '<h', '<i', '<p', '<a',
                '<thead', '<td', '<tr', '<th', '<table', '<tbody',
                '<span', '<button',
            ],
            []
        );

        if ($result > 0 && $this->debug) {
            echo "Line $lineNumber is opening line" . PHP_EOL;
        }

        return $result;
    }

    private function isSpecialLine($line, $lineNumber)
    {
        $result = $this->doesLineMatches(
            $line,
            [
                '{% else',
            ],
            []
        );

        if ($result > 0 && $this->debug) {
            echo "Line $lineNumber is opening line" . PHP_EOL;
        }

        return $result;
    }

    private function isClosingLine($line, $lineNumber)
    {
        $result = $this->doesLineMatches(
            $line,
            [
                '{% endblock', '{% endif', '{% endfor',
                '</div',
                '</h', '</i', '</p', '</a',
                '</thead', '</td', '</tr', '</th', '</table', '</tbody',
                '</span', '</button'
            ],
            []
        );

        if ($result > 0 && $this->debug) {
            echo "Line $lineNumber is closing line" . PHP_EOL;
        }

        return $result;
    }

    private function doesLineMatches($line, $contains = [], $patterns = [])
    {
        $level = 0;

        foreach ($contains as $contain) {
            $level += substr_count($line, $contain);
        }

        foreach ($patterns as $pattern) {
            $level += substr_count($line, $contain);
        }

        return $level;
    }

    private function putXBlankSpace($number)
    {
        $result = str_repeat(" ", $number);

        return $result;
    }

    private function incrementIndentationLevel()
    {
        $this->currentIndentationLevel += $this->numberOfSpaces;

        if ($this->debug) {
            echo "Indentation level raised to " . $this->currentIndentationLevel . PHP_EOL;
        }
    }

    private function decrementIndentationLevel()
    {
        $this->currentIndentationLevel -= $this->numberOfSpaces;
        if ($this->currentIndentationLevel < 0) {
            $this->currentIndentationLevel = 0;
        }

        if ($this->debug) {
            echo "Indentation level decreased to " . $this->currentIndentationLevel . PHP_EOL;
        }
    }
}
