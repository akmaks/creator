<?php

namespace Akimmaksimov85\CreatorBundle\Command;

use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\EntityDTO\Command as EntityDTOCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\EntityDTO\Interactor as EntityDTOInteractor;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateEntityDTOCommand extends AbstractGenerateCommand
{
    protected static $defaultName = 'generator:entityDTO';

    protected function configure()
    {
        $this
            ->setDescription('generator:entityDTO --file Client/Client --properties id:int/name:string/url:string')
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

        $command = new EntityDTOCommand();
        $command->folder = $this->folderPath;
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;

        (new EntityDTOInteractor())($command);

        return 0;
    }
}