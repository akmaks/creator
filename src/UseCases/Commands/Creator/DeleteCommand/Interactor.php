<?php

namespace Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DeleteCommand;

use Akimmaksimov85\CreatorBundle\Entity\UseCaseCreateCommandCreator;
use Akimmaksimov85\CreatorBundle\Entity\UseCaseDeleteCommandCreator;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\AbstractInteractor;

class Interactor extends AbstractInteractor
{
    /**
     * Command handler
     *
     * @param Command $command Command
     *
     * @return void
     */
    public function __invoke(Command $command) : void
    {
        $creator = new UseCaseDeleteCommandCreator(
            $command->folder,
            $command->fileName,
            $command->properties
        );
        $creator->run();
    }

}
