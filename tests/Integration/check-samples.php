<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Matks\PHPTemplateLinter\LinterManager;
use MatksTests\TestUtils;

$debug = true;
$linter = new LinterManager(null, $debug);

$filesToTest = [
    /*'twig1.html.twig',
    'twig2.html.twig',
    'twig3.html.twig',
    'twig4.html.twig',
    'twig5.html.twig',
    'twig6.html.twig',*/
    'twig7.html.twig',
];

foreach ($filesToTest as $file) {
    $sample = __DIR__ . '/samples/' . $file;
    $expected = __DIR__ . '/expected/' . $file;

    echo 'Testing ' . $file . PHP_EOL;

    $linted = $linter->getLintedFileContent($sample, LinterManager::TYPE_TWIG);
    $expected_content = file_get_contents($expected);

    $result = TestUtils::compareLineByLine(
        $linted,
        $expected_content,
        '(linted) ' . basename($sample),
        '(expected) ' . basename($expected),
        $linter
    );

    if ($result === false) {
        echo 'Failure !'.PHP_EOL;
        return 255;
    }

    echo PHP_EOL;
}

echo 'All configuration tests pass !' . PHP_EOL;
return 0;
