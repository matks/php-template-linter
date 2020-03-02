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
                '<h', '<i>', '<p', '<a', '<i ',
                '<thead', '<td', '<tr', '<th', '<table', '<tbody',
                '<span', '<button', '<label',
                '<ul', '<li', '<select',
            ],
            LineLinterConfigurationItem::TYPE_OPEN_AND_CLOSE_CHAR => [
                '{% else'
            ],
            LineLinterConfigurationItem::TYPE_CLOSING_CHAR => [
                '{% endblock', '{% endif', '{% endfor',
                '</div', '</form',
                '</h', '</i>', '</p', '</a', '</i ',
                '</thead', '</td', '</tr', '</th', '</table', '</tbody',
                '</span', '</button', '</label',
                '</ul', '</li', '</select',
            ],
        ];

        return LineLinterConfiguration::fromArray($configuration, self::DEFAULT_INDENTATION_LEVEL);
    }
}
