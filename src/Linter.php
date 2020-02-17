<?php

class Linter
{
    private $currentIndentationLevel = 0;
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

        if ($handle) {
            while (($line = fgets($handle)) !== false) {

                $lintedLine = $this->processLine($line, $lineNumber);

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
        if ($this->shouldIgnoreThisLine($line, $lineNumber)) {
            return $line;
        }
        $noSpaceLine = ltrim($line);

        $isOpeningLine = $this->isOpeningLine($noSpaceLine, $lineNumber);
        $isClosingLine = $this->isClosingLine($noSpaceLine, $lineNumber);

        if (!$isOpeningLine && $isClosingLine) {
            $this->decrementIndentationLevel();
        }

        $indentedLine = $this->putXBlankSpace($this->currentIndentationLevel) . $noSpaceLine;

        if ($isOpeningLine && !$isClosingLine) {
            $this->incrementIndentationLevel();
        }

        return $indentedLine;
    }

    private function shouldIgnoreThisLine($line, $lineNumber)
    {
        $result = $this->doesLineMatches(
            $line,
            [
                '{#',
                '*'
            ],
            []
        );

        if ($result && $this->debug) {
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
                '<h', '<i', '<p',
                '<thead', '<td', '<tr', '<th', '<table', '<tbody'
            ],
            []
        );

        if ($result && $this->debug) {
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
                '</h', '</i', '</p',
                '</thead', '</td', '</tr', '</th', '</table', '</tbody'
            ],
            []
        );

        if ($result && $this->debug) {
            echo "Line $lineNumber is closing line" . PHP_EOL;
        }

        return $result;
    }

    private function doesLineMatches($line, $contains = [], $patterns = [])
    {
        foreach ($contains as $contain) {
            if (strpos($line, $contain) !== false) {
                return true;
            }
        }

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }

        return false;
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
