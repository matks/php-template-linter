<?php

namespace Matks\PHPTemplateLinter;

class LineLinterConfigurationProcessor
{
    /** @var string[] */
    private $ignoreChars = [];
    /** @var string[] */
    private $openingChars = [];
    /** @var string[] */
    private $openAndCloseChars = [];
    /** @var string[] */
    private $closingChars = [];
    /** @var int */
    private $indentationLevel;

    /**
     * @param LineLinterConfiguration $configuration
     */
    public function loadConfiguration(LineLinterConfiguration $configuration)
    {
        foreach ($configuration->items as $item) {

            switch ($item->type) {
                case LineLinterConfigurationItem::TYPE_IGNORE_CHAR:
                    $this->ignoreChars[$item->string] = $item->string;
                    break;

                case LineLinterConfigurationItem::TYPE_CLOSING_CHAR:
                    $this->closingChars[$item->string] = $item->string;
                    break;

                case LineLinterConfigurationItem::TYPE_OPEN_AND_CLOSE_CHAR:
                    $this->openAndCloseChars[$item->string] = $item->string;
                    break;

                case LineLinterConfigurationItem::TYPE_OPENING_CHAR:
                    $this->openingChars[$item->string] = $item->string;
                    break;

                default:
                    throw new \RuntimeException(sprintf('Unknown configuration item type %s', $item->type));
            }

        }

        $this->indentationLevel = $configuration->indentationLevel;
    }

    /**
     * @return string[]
     */
    public function getIgnoreChars()
    {
        return $this->ignoreChars;
    }

    /**
     * @return string[]
     */
    public function getOpeningChars()
    {
        return $this->openingChars;
    }

    /**
     * @return string[]
     */
    public function getOpenAndCloseChars()
    {
        return $this->openAndCloseChars;
    }

    /**
     * @return string[]
     */
    public function getClosingChars()
    {
        return $this->closingChars;
    }

    /**
     * @return int
     */
    public function getIndentationLevel()
    {
        return $this->indentationLevel;
    }
}
