<?php

namespace Akimmaksimov85\CreatorBundle\Command;

use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DoctrineEntityRepository\Command as DoctrineEntityRepositoryCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DoctrineEntityRepository\Interactor as DoctrineEntityRepositoryInteractor;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateDoctrineEntityRepositoryCommand extends AbstractGenerateCommand
{
    protected static $defaultName = 'generator:doctrineEntityRepository';

    protected function configure()
    {
        $this
            ->setDescription('generator:doctrineEntityRepository --file Client/Client --properties id:string/name:string/url:string')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('file', null, InputOption::VALUE_REQUIRED),
                    new InputOption('properties', null, InputOption::VALUE_REQUIRED),
                ])
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->parseData($input->getOptions()['file'], $input->getOptions()['properties']);

        $command = new DoctrineEntityRepositoryCommand();
        $command->folder = $this->folderPath;
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;

        (new DoctrineEntityRepositoryInteractor())($command);

        return 0;
    }
}