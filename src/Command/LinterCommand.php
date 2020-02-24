<?php

namespace Matks\PHPTemplateLinter\Command;

use Matks\PHPTemplateLinter\DefaultConfiguration;
use Matks\PHPTemplateLinter\FileExplorator;
use Matks\PHPTemplateLinter\LineLinterConfiguration;
use Matks\PHPTemplateLinter\LinterManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LinterCommand extends Command
{
    protected static $defaultName = 'fix';

    protected function configure()
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Fix target directory indentation for templating files (twig or smarty)')
            ->addArgument(
                'target',
                InputArgument::REQUIRED,
                'Target directory to fix'
            )
            ->addOption('type',
                null,
                InputOption::VALUE_OPTIONAL,
                'smarty or twig', 'twig'
            )
            ->addOption('config',
                'c',
                InputOption::VALUE_REQUIRED,
                'config file to use', false
            )
            ->addOption('dry-run',
                null,
                InputOption::VALUE_OPTIONAL,
                'Whether or not to modify parsed files', false
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $target = $input->getArgument('target');
        $config = $input->getOption('config');
        $type = $input->getOption('type');
        $dryRun = false !== $input->getOption('dry-run');

        LinterManager::validateType($type);

        if ($config) {
            $configuration = $this->loadProvidedConfiguration($config);
        } else {
            $configuration = DefaultConfiguration::get();
        }

        $explorator = new FileExplorator();
        $filesList = $explorator->findAllFilesInTarget($target, $type);

        if (empty($filesList)) {
            $output->writeln(sprintf('No eligible files found in %s', $target));
            return 0;
        }

        $linterManager = new LinterManager($configuration);

        foreach ($filesList as $file) {
            $linterManager->lintFile($file, $type, $dryRun);
        }

        return 0;
    }

    private function loadProvidedConfiguration($configurationFilepath)
    {
        if (!file_exists($configurationFilepath)) {
            throw new \InvalidArgumentException(sprintf('Provided configuration file %s does not exist', $configurationFilepath));
        }

        $loaded = require_once $configurationFilepath;

        if (false === ($loaded instanceof LineLinterConfiguration)) {
            throw new \InvalidArgumentException(sprintf('Provided configuration file must return a LineLinterConfiguration instance'));
        }

        return $loaded;
    }
}
