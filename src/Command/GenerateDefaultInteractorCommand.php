<?php

namespace Akimmaksimov85\CreatorBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DefaultInteractor\Command as DefaultInteractorCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DefaultInteractor\Interactor as DefaultInteractorInteractor;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'generator:defaultInteractor')]
class GenerateDefaultInteractorCommand extends AbstractGenerateCommand
{
    /**
     * @var string
     */
    protected static $defaultName = 'generator:defaultInteractor';

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setDescription('generator:defaultInteractor --file Client/Client --properties id:int/name:string/url:string')
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

        $command = new DefaultInteractorCommand();
        $command->folder = $this->folderPath;
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;

        (new DefaultInteractorInteractor())($command);

        return 0;
    }
}