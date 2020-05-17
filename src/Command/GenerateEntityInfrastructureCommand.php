<?php

namespace Akimmaksimov85\CreatorBundle\Command;

use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\CommandCommand\Command as CommandCommandCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\CommandCommand\Interactor as CommandCommandInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DefaultInteractor\Command as DefaultInteractorCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DefaultInteractor\Interactor as DefaultInteractorInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\CreateInteractor\Command as CreateInteractorCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\CreateInteractor\Interactor as CreateInteractorInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\UpdateInteractor\Command as UpdateInteractorCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\UpdateInteractor\Interactor as UpdateInteractorInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DeleteInteractor\Command as DeleteInteractorCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DeleteInteractor\Interactor as DeleteInteractorInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DoctrineGateway\Command as DoctrineGatewayCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DoctrineGateway\Interactor as DoctrineGatewayInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\Entity\Command as EntityCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\Entity\Interactor as EntityInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\EntityDTO\Command as EntityDTOCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\EntityDTO\Interactor as EntityDTOInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\EntityNotFoundByIdException\Command as EntityNotFoundByIdExceptionCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\EntityNotFoundByIdException\Interactor as EntityNotFoundByIdExceptionInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\GatewayInterface\Command as GatewayInterfaceCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\GatewayInterface\Interactor as GatewayInterfaceInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DoctrineEntity\Command as DoctrineEntityCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DoctrineEntity\Interactor as DoctrineEntityInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DoctrineEntityRepository\Command as DoctrineEntityRepositoryCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DoctrineEntityRepository\Interactor as DoctrineEntityRepositoryInteractor;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateEntityInfrastructureCommand extends AbstractGenerateCommand
{
    protected static $defaultName = 'generator:entityInfrastructure';

    protected function configure()
    {
        $this
            ->setDescription('generator:entityInfrastructure --file Client --properties id:string/name:string/url:string')
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

        $command = new EntityCommand();
        $command->folder = 'Entities/' . $this->fileName;
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;
        (new EntityInteractor())($command);

        $command = new GatewayInterfaceCommand();
        $command->folder = 'Entities/' . $this->fileName;
        $command->entity = $this->fileName;
        (new GatewayInterfaceInteractor())($command);

        $command = new EntityDTOCommand();
        $command->folder = 'Entities/' . $this->fileName;
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;
        (new EntityDTOInteractor())($command);

        $command = new EntityNotFoundByIdExceptionCommand();
        $command->folder = 'Data/Exceptions/' . $this->fileName;
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;
        (new EntityNotFoundByIdExceptionInteractor())($command);

        $command = new DoctrineEntityCommand();
        $command->folder = 'Data/Gateways/Doctrine/' . $this->fileName;
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;
        (new DoctrineEntityInteractor())($command);

        $command = new DoctrineEntityRepositoryCommand();
        $command->folder = 'Data/Gateways/Doctrine/' . $this->fileName;
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;
        (new DoctrineEntityRepositoryInteractor())($command);

        $command = new DoctrineGatewayCommand();
        $command->folder = 'Data/Gateways/Doctrine/' . $this->fileName;
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;
        (new DoctrineGatewayInteractor())($command);

        $command = new CommandCommandCommand();
        $command->folder = 'UseCases/Command/' . $this->fileName . '/Create';
        $command->fileName = 'Command';
        $command->properties = $this->properties;
        (new CommandCommandInteractor())($command);

        $command = new CommandCommandCommand();
        $command->folder = 'UseCases/Command/' . $this->fileName . '/Update';
        $command->fileName = 'Command';
        $command->properties = $this->properties;
        (new CommandCommandInteractor())($command);

        $command = new CommandCommandCommand();
        $command->folder = 'UseCases/Command/' . $this->fileName . '/Delete';
        $command->fileName = 'Command';
        $command->properties = $this->properties;
        (new CommandCommandInteractor())($command);

        $command = new CreateInteractorCommand();
        $command->folder = 'UseCases/Command/' . $this->fileName . '/Create';
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;
        (new CreateInteractorInteractor())($command);

        $command = new UpdateInteractorCommand();
        $command->folder = 'UseCases/Command/' . $this->fileName . '/Update';
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;
        (new UpdateInteractorInteractor())($command);

        $command = new DeleteInteractorCommand();
        $command->folder = 'UseCases/Command/' . $this->fileName . '/Delete';
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;
        (new DeleteInteractorInteractor())($command);

        $command = new CommandCommandCommand();
        $command->folder = 'UseCases/Query/' . $this->fileName . '/FindAll';
        $command->fileName = 'Command';
        $command->properties = [
            'page' => 'int',
            'limit' => 'int',
        ];
        (new CommandCommandInteractor())($command);

        $command = new DefaultInteractorCommand();
        $command->folder = 'UseCases/Query/' . $this->fileName . '/FindAll';
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;
        (new DefaultInteractorInteractor())($command);

        return 0;
    }
}