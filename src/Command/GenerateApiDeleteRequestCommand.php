<?php

namespace Akimmaksimov85\CreatorBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DeleteRequest\Command;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DeleteRequest\Interactor;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'generator:deleteRequest')]
class GenerateApiDeleteRequestCommand extends AbstractGenerateCommand
{
    protected static $defaultName = 'generator:deleteRequest';

    protected function configure()
    {
        $this
            ->setDescription('generator:deleteRequest --file Client/Client --properties id:int/name:string/url:string')
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

        $command = new Command();
        $command->folder = 'UI/API/Requests/' . $this->fileName;
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;

        (new Interactor())($command);

        return 0;
    }
}