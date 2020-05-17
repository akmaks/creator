<?php

namespace Akimmaksimov85\CreatorBundle\Command;

use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\GatewayInterface\Command;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\GatewayInterface\Interactor;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateGatewayInterfaceCommand extends AbstractGenerateCommand
{
    protected static $defaultName = 'generator:gatewayInterface';

    protected function configure()
    {
        $this
            ->setDescription('generator:gatewayInterface --file Client/Client')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('file', null, InputOption::VALUE_REQUIRED)
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
        $this->parseData($input->getOptions()['file']);

        $command = new Command();
        $command->folder = $this->folderPath;
        $command->entity = $this->fileName;

        (new Interactor())($command);

        return 0;
    }
}