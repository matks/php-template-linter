<?php

namespace Matks\PHPTemplateLinter;


class DefaultConfiguration
{
    const DEFAULT_INDENTATION_LEVEL = 2;

    /**
     * @return LineLinterConfiguration
     */
    public static function get()
    {
        $configuration = [
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

        $configurationItems = [];

        foreach ($configuration as $configurationType => $items) {
            foreach ($items as $item) {
                $configurationItems[] = new LineLinterConfigurationItem(
                    $item,
                    $configurationType
                );
            }
        }

        return new LineLinterConfiguration($configurationItems, self::DEFAULT_INDENTATION_LEVEL);
    }
}
