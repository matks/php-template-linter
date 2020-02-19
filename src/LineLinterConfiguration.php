<?php

namespace Matks\PHPTemplateLinter;

class LineLinterConfiguration
{
    /** @var LineLinterConfigurationItem[] */
    public $items;
    /** @var int */
    public $indentationLevel;

    /**
     * @param LineLinterConfigurationItem[] $items
     * @param int $indentationLevel
     */
    public function __construct(array $items, $indentationLevel)
    {
        foreach ($items as $item) {
            $this->items[$item->string] = $item;
        }

        $this->indentationLevel = $indentationLevel;
    }

    /**
     * @param LineLinterConfigurationItem $item
     */
    public function addItem(LineLinterConfigurationItem $item)
    {
        $this->items[$item->string] = $item;
    }
}
