<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Matks\PHPTemplateLinter\LinterManager;
use MatksTests\Integration\TestUtils;

$linter = new LinterManager();

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

    $linted = $linter->lintFile($sample, LinterManager::TYPE_TWIG);
    $expected_content = file_get_contents($expected);

    TestUtils::compareLineByLine(
        $linted,
        $expected_content,
        '(linted) ' . basename($sample),
        '(expected) ' . basename($expected),
        $linter
    );

    echo PHP_EOL;
}


die('All tests pass !' . PHP_EOL);
