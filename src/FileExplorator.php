<?php

namespace Matks\PHPTemplateLinter;

use Symfony\Component\Finder\Finder;

class FileExplorator
{
    /**
     * @param string $target
     * @param string $type
     *
     * @return string[]
     */
    public function findAllFilesInTarget($target, $type)
    {
        $extension = null;
        switch ($type) {
            case LinterManager::TYPE_TWIG:
                $extension = '.twig';
                break;

            case LinterManager::TYPE_SMARTY:
                $extension = '.tpl';
                break;

            default:
                throw new \RuntimeException(sprintf('Unknown file type %s', $type));
        }

        $finder = new Finder();
        $finder->files()->in($target)->name('*' . $extension);

        if (!$finder->hasResults()) {
            return [];
        }

        $results = [];

        foreach ($finder as $file) {
            $results[] = $file->getRealPath();
        }

        return $results;
    }


}
