<?php


function compareLineByLine($string1, $string2, $filename1, $filename2, $linter)
{
    $lines1 = explode(PHP_EOL, $string1);
    $lines2 = explode(PHP_EOL, $string2);

    if (count($lines1) !== count($lines2)) {
        echo "2 given files do not have same number of lines !" . PHP_EOL;
        echo " - file $filename1 has " . count($lines1) . ". lines" . PHP_EOL;
        echo " - file $filename2 has " . count($lines2) . ". lines" . PHP_EOL;
        die();
    }

    for ($x = 0; $x < count($lines1); $x++) {

        $line1 = $lines1[$x];
        $line2 = $lines2[$x];

        if ($line1 != $line2) {
            $lineNumber = $x + 1;
            $indent1 = findIndentationLevel($line1);
            $indent2 = findIndentationLevel($line2);

            echo "Line $lineNumber differ !" . PHP_EOL;
            echo "- (linted - indent $indent1) :" . $line1 . PHP_EOL;
            echo "- (expected  - indent $indent2) :" . $line2 . PHP_EOL;
            echo "Last indentation level was " . $linter->getCurrentIndentationLevel() . PHP_EOL;
            die();
        }
    }
}

function findIndentationLevel($line)
{
    return strlen(ltrim($line)) - strlen($line);
}


require_once __DIR__ . '/../src/Linter.php';

$linter = new Linter();

$sample1 = __DIR__ . '/samples/twig1.html.twig';
$expected1 = __DIR__ . '/expected/twig1.html.twig';

$linted1 = $linter->getLintedFileContent($sample1, 'twig');
$expected_content = file_get_contents($expected1);

compareLineByLine(
    $linted1,
    $expected_content,
    '(linted) ' . basename($sample1),
    '(expexted) ' . basename($expected1),
    $linter
);

die('ok' . PHP_EOL);
