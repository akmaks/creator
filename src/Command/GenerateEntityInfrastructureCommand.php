<?php

namespace Akimmaksimov85\CreatorBundle\Command;

use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\CreateCommand\Interactor as CreateCommandInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\UpdateCommand\Interactor as UpdateCommandInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DeleteCommand\Interactor as DeleteCommandInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\CreateCommand\Command as CreateCommandCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\UpdateCommand\Command as UpdateCommandCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DeleteCommand\Command as DeleteCommandCommand;

use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\CreateRequest\Interactor as CreateRequestInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\UpdateRequest\Interactor as UpdateRequestInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DeleteRequest\Interactor as DeleteRequestInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\CreateRequest\Command as CreateRequestCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\UpdateRequest\Command as UpdateRequestCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DeleteRequest\Command as DeleteRequestCommand;

use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\CreateInteractor\Interactor as CreateInteractorInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\UpdateInteractor\Interactor as UpdateInteractorInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DeleteInteractor\Interactor as DeleteInteractorInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\CreateInteractor\Command as CreateInteractorCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\UpdateInteractor\Command as UpdateInteractorCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DeleteInteractor\Command as DeleteInteractorCommand;

use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DoctrineGateway\Command as DoctrineGatewayCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DoctrineEntityRepository\Command as DoctrineEntityRepositoryCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DoctrineGateway\Interactor as DoctrineGatewayInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DoctrineEntityRepository\Interactor as DoctrineEntityRepositoryInteractor;

use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\Entity\Command as EntityCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\EntityDTO\Command as EntityDTOCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\GatewayInterface\Command as GatewayInterfaceCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\Entity\Interactor as EntityInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\EntityDTO\Interactor as EntityDTOInteractor;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\GatewayInterface\Interactor as GatewayInterfaceInteractor;

use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\EntityNotFoundByIdException\Command as EntityNotFoundByIdExceptionCommand;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\EntityNotFoundByIdException\Interactor as EntityNotFoundByIdExceptionInteractor;

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
            ->setDescription('generator:entityInfrastructure --file Client --properties id:int/name:string/url:string')
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

        $command = new CreateCommandCommand();
        $command->folder = 'UseCases/Command/' . $this->fileName . '/Create';
        $command->fileName = 'Command';
        $command->properties = $this->properties;
        (new CreateCommandInteractor())($command);

        $command = new UpdateCommandCommand();
        $command->folder = 'UseCases/Command/' . $this->fileName . '/Update';
        $command->fileName = 'Command';
        $command->properties = $this->properties;
        (new UpdateCommandInteractor())($command);

        $command = new DeleteCommandCommand();
        $command->folder = 'UseCases/Command/' . $this->fileName . '/Delete';
        $command->fileName = 'Command';
        $command->properties = $this->properties;
        (new DeleteCommandInteractor())($command);

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

        $command = new CreateRequestCommand();
        $command->folder = 'UI/API/Requests/' . $this->fileName;
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;
        (new CreateRequestInteractor())($command);

        $command = new UpdateRequestCommand();
        $command->folder = 'UI/API/Requests/' . $this->fileName;
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;
        (new UpdateRequestInteractor())($command);

        $command = new DeleteRequestCommand();
        $command->folder = 'UI/API/Requests/' . $this->fileName;
        $command->fileName = $this->fileName;
        $command->properties = $this->properties;
        (new DeleteRequestInteractor())($command);

        return 0;
    }
}