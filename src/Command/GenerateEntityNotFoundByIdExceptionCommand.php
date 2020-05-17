<?php

namespace Akimmaksimov85\CreatorBundle\Command;

use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\EntityNotFoundByIdException\Command as EntityNotFoundByIdExceptionCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\EntityNotFoundByIdException\Interactor as EntityNotFoundByIdExceptionInteractor;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateEntityNotFoundByIdExceptionCommand extends AbstractGenerateCommand
{
    protected static $defaultName = 'generator:entityNotFoundByIdException';

    protected function configure()
    {
        $this
            ->setDescription('generator:entityNotFoundByIdException --file Data/Exceptions/Client --properties id:string')
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

        $command = new EntityNotFoundByIdExceptionCommand();
        $command->folder = $this->folderPath;
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;

        (new EntityNotFoundByIdExceptionInteractor())($command);

        return 0;
    }
}