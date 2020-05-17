<?php

namespace Akimmaksimov85\CreatorBundle\Command;

use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DoctrineEntity\Command as DoctrineEntityCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DoctrineEntity\Interactor as DoctrineEntityInteractor;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateDoctrineEntityCommand extends AbstractGenerateCommand
{
    protected static $defaultName = 'generator:doctrineEntity';

    protected function configure()
    {
        $this
            ->setDescription('generator:doctrineEntity --file Data/Gateways/Doctrine/Client/Client --properties id:string/name:string/url:string')
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

        $command = new DoctrineEntityCommand();
        $command->folder = $this->folderPath;
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;

        (new DoctrineEntityInteractor())($command);

        return 0;
    }
}