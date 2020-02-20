<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\Console\Application;
use MatksTests\TestUtils;

TestUtils::copySampleFolderIntoWorkspace();

try {
    $application = new Application();
    $application->setAutoExit(false);
    $application->add(new \Matks\PHPTemplateLinter\Command\LinterCommand());

    $input = new \Symfony\Component\Console\Input\ArrayInput(
        [
            'command' => 'fix',
            'target' => __DIR__ . '/workspace',
        ]
    );
    $result = $application->run($input, new \Symfony\Component\Console\Output\NullOutput());

    $filesToTest = [
        'twig1.html.twig',
        'twig2.html.twig',
        'twig3.html.twig',
        'twig4.html.twig',
        'twig5.html.twig',
    ];

    foreach ($filesToTest as $file) {
        $sample = __DIR__ . '/workspace/' . $file;
        $expected = __DIR__ . '/expected/' . $file;

        echo 'Testing ' . $file . PHP_EOL;

        $linted = file_get_contents($sample);
        $expected_content = file_get_contents($expected);

        $result = TestUtils::compareLineByLine(
            $linted,
            $expected_content,
            '(linted) ' . basename($sample),
            '(expected) ' . basename($expected),
            null
        );

        if (false === $result) {
            echo 'Failure !';
            return 255;
        }

        echo PHP_EOL;
    }


    TestUtils::emptyWorkspaceFolder();
} catch (\Exception $e) {
    TestUtils::emptyWorkspaceFolder();

    throw $e;
}

echo 'All acceptance tests pass !' . PHP_EOL;
return 0;

