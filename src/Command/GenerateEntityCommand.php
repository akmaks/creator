<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\Command;

use Akimmaksimov85\CreatorBundle\Providers\EntityDataProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'generator:entity')]
class GenerateEntityCommand extends AbstractGenerateCommand
{
    protected static $defaultName = 'generator:entity';

    protected function configure()
    {
        $this
            ->setDescription(
                'generator:entity --file Entities/Client/Client --properties id:int/name:string/url:string'
            )
            ->setDefinition(
                new InputDefinition(
                    [
                        new InputOption('file', null, InputOption::VALUE_REQUIRED),
                        new InputOption('properties', null, InputOption::VALUE_REQUIRED),
                    ]
                )
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
        $dataProvider = $this->getDataProviderFactory($input)->get(EntityDataProvider::class);

        return $this->runInteractor($dataProvider, $this->properties);
    }
}