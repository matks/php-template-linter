<?php

/**
 * Keep empty lines, contrary to explode()
 *
 * @param $string
 * @return array[]|false|string[]
 */
function splitStringIntoArray($string)
{
    return explode(PHP_EOL, $string);
}

function compareLineByLine($string1, $string2, $filename1, $filename2, $linter)
{
    $lines1 = splitStringIntoArray($string1);
    $lines2 = splitStringIntoArray($string2);

    if (count($lines1) !== count($lines2)) {
        echo "2 given files do not have same number of lines !" . PHP_EOL;
        echo " - file $filename1 has " . count($lines1) . " lines" . PHP_EOL;
        echo " - file $filename2 has " . count($lines2) . " lines" . PHP_EOL;
    }

    $report = $linter->getLatestReport();

    for ($x = 0; $x < count($lines1); $x++) {

        $line1 = $lines1[$x];
        $line2 = $lines2[$x];

        if (isset($report[$x]) && $report[$x] === \Matks\PHPTemplateLinter\LineLinter::OPERATION_IGNORED_BECAUSE_MULTILINE) {
            continue;
        }
        if (isset($report[$x]) && $report[$x] === \Matks\PHPTemplateLinter\LineLinter::OPERATION_IGNORED) {
            continue;
        }

        if (($line1 != $line2) && (($line1 !== '') && ($line2 !== '')) ) {
            $lineNumber = $x + 1;
            $indent1 = findIndentationLevel($line1);
            $indent2 = findIndentationLevel($line2);

            echo "Line $lineNumber differ !" . PHP_EOL;
            echo "- (linted - indent $indent1) :" . $line1 . PHP_EOL;
            echo "- (expected  - indent $indent2) :" . $line2 . PHP_EOL;
            die();
        }
    }
}

function findIndentationLevel($line)
{
    $result = strlen($line) - strlen(ltrim($line));

    if ($result < 0) {
        return 0;
    }

    return $result;
}


require_once __DIR__ . '/../../vendor/autoload.php';

$linter = new \Matks\PHPTemplateLinter\LinterManager();

$filesToTest = [
    'twig1.html.twig',
    'twig2.html.twig',
    'twig3.html.twig',
    'twig4.html.twig',
    'twig5.html.twig',
];

foreach ($filesToTest as $file) {
    $sample = __DIR__ . '/samples/' . $file;
    $expected = __DIR__ . '/expected/' . $file;

    echo 'Testing ' . $file . PHP_EOL;

    $linted = $linter->lintFile($sample, \Matks\PHPTemplateLinter\LinterManager::TYPE_TWIG);
    $expected_content = file_get_contents($expected);

    compareLineByLine(
        $linted,
        $expected_content,
        '(linted) ' . basename($sample),
        '(expected) ' . basename($expected),
        $linter
    );

    echo PHP_EOL;
}


die('All tests pass !' . PHP_EOL);
