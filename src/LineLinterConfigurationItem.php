<?php

namespace Matks\PHPTemplateLinter;

class LineLinterConfigurationItem
{
    const TYPE_OPENING_CHAR = 'opening';
    const TYPE_OPEN_AND_CLOSE_CHAR = 'open-and-close';
    const TYPE_CLOSING_CHAR = 'closing';
    const TYPE_IGNORE_CHAR = 'ignore';

    /** @var string */
    public $string;
    /** @var string */
    public $type;

    /**
     * @param string $string
     * @param string $type
     */
    public function __construct($string, $type)
    {
        $availableTypes = [
            self::TYPE_OPENING_CHAR,
            self::TYPE_OPEN_AND_CLOSE_CHAR,
            self::TYPE_CLOSING_CHAR,
            self::TYPE_IGNORE_CHAR,
        ];

        if (!in_array($type, $availableTypes)) {
            throw new \InvalidArgumentException(sprintf('Type must be one of %s', implode(', ', $availableTypes)));
        }

        $this->string = $string;
        $this->type = $type;
    }
}
