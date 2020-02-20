<?php

namespace MatksTests;

use Matks\PHPTemplateLinter\LinterManager;
use Symfony\Component\Filesystem\Filesystem;

class TestUtils
{
    /**
     * @param string $string
     *
     * @return array[]|false|string[]
     */
    public static function splitStringIntoArray($string)
    {
        return explode(PHP_EOL, $string);
    }

    /**
     * @param string $string1
     * @param string $string2
     * @param string $filename1
     * @param string $filename2
     * @param LinterManager $linter
     *
     * @param LinterManager $linter
     */
    public static function compareLineByLine($string1, $string2, $filename1, $filename2, $linter = null)
    {
        $lines1 = self::splitStringIntoArray($string1);
        $lines2 = self::splitStringIntoArray($string2);

        if (count($lines1) !== count($lines2)) {
            echo "2 given files do not have same number of lines !" . PHP_EOL;
            echo " - file $filename1 has " . count($lines1) . " lines" . PHP_EOL;
            echo " - file $filename2 has " . count($lines2) . " lines" . PHP_EOL;
            return false;
        }

        $report = null;
        if (null !== $linter) {
            $report = $linter->getLatestReport();
        }

        for ($x = 0; $x < count($lines1); $x++) {

            $line1 = $lines1[$x];
            $line2 = $lines2[$x];

            if (isset($report[$x]) && $report[$x] === \Matks\PHPTemplateLinter\LineLinter::OPERATION_IGNORED_BECAUSE_MULTILINE) {
                continue;
            }
            if (isset($report[$x]) && $report[$x] === \Matks\PHPTemplateLinter\LineLinter::OPERATION_IGNORED) {
                continue;
            }

            if (($line1 != $line2) && (($line1 !== '') && ($line2 !== ''))) {
                $lineNumber = $x + 1;
                $indent1 = self::findIndentationLevel($line1);
                $indent2 = self::findIndentationLevel($line2);

                echo "Line $lineNumber differ !" . PHP_EOL;
                echo "- (linted - indent $indent1) :" . $line1 . PHP_EOL;
                echo "- (expected  - indent $indent2) :" . $line2 . PHP_EOL;
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $line
     *
     * @return int
     */
    public static function findIndentationLevel($line)
    {
        $result = strlen($line) - strlen(ltrim($line));

        if ($result < 0) {
            return 0;
        }

        return $result;
    }

    public static function copySampleFolderIntoWorkspace()
    {
        $filesystem = new Filesystem();
        $filesystem->mkdir(__DIR__ . '/Acceptance/workspace/');
        $filesystem->mirror(__DIR__ . '/Acceptance/samples', __DIR__ . '/Acceptance/workspace/');
    }

    public static function emptyWorkspaceFolder()
    {
        $filesystem = new Filesystem();
        $filesystem->remove(__DIR__ . '/Acceptance/workspace/');
        $filesystem->mkdir(__DIR__ . '/Acceptance/workspace/');
    }
}
