<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Matks\PHPTemplateLinter\LinterManager;
use MatksTests\TestUtils;
use Matks\PHPTemplateLinter\LineLinterConfigurationItem;
use Matks\PHPTemplateLinter\LineLinterConfiguration;

$configuration1 = [
    LineLinterConfigurationItem::TYPE_IGNORE_CHAR => [
        '{#', '*'
    ],
    LineLinterConfigurationItem::TYPE_OPENING_CHAR => [
        '{% block', '{% if', '{% for',
        '<div', '<form',
        '<h', '<i', '<p', '<a',
        '<thead', '<td', '<tr', '<th', '<table', '<tbody',
        '<span', '<button', '<label',
    ],
    LineLinterConfigurationItem::TYPE_OPEN_AND_CLOSE_CHAR => [
        '{% else'
    ],
    LineLinterConfigurationItem::TYPE_CLOSING_CHAR => [
        '{% endblock', '{% endif', '{% endfor',
        '</div', '</form',
        '</h', '</i', '</p', '</a',
        '</thead', '</td', '</tr', '</th', '</table', '</tbody',
        '</span', '</button', '</label',
    ],
];

$configuration2 = [
    LineLinterConfigurationItem::TYPE_IGNORE_CHAR => [
        '*'
    ],
    LineLinterConfigurationItem::TYPE_OPENING_CHAR => [
        '<div', '<table', '{#'
    ],
    LineLinterConfigurationItem::TYPE_OPEN_AND_CLOSE_CHAR => [],
    LineLinterConfigurationItem::TYPE_CLOSING_CHAR => [
        '</div', '</table', '#}'
    ],
];

$linters = [
    new LinterManager(LineLinterConfiguration::fromArray($configuration1, 2)),
    new LinterManager(LineLinterConfiguration::fromArray($configuration1, 4)),
    new LinterManager(LineLinterConfiguration::fromArray($configuration2, 1)),
];

$sampleName = 'twig1.html.twig';
$filesToTest = [
    'twig1.html.twig',
    'twig1-4indent.html.twig',
    'twig1-1specialindent.html.twig',
];

foreach ($filesToTest as $i => $expectedName) {
    $sample = __DIR__ . '/samples/' . $sampleName;
    $expected = __DIR__ . '/expected/' . $expectedName;

    echo 'Testing ' . $sampleName . ' with configuration ' . ($i + 1) . PHP_EOL;

    $linted = $linters[$i]->getLintedFileContent($sample, LinterManager::TYPE_TWIG);
    $expected_content = file_get_contents($expected);

    $result = TestUtils::compareLineByLine(
        $linted,
        $expected_content,
        '(linted) ' . basename($sample),
        '(expected) ' . basename($expected),
        $linters[$i]
    );

    if ($result === false) {
        echo 'Failure !'.PHP_EOL;
        return 255;
    }

    echo PHP_EOL;
}


echo 'All configuration tests pass !' . PHP_EOL;
return 0;
