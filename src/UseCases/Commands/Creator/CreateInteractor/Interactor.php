<?php

namespace Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\CreateInteractor;

use Akimmaksimov85\CreatorBundle\Entity\UseCaseCreateInteractorCreator;
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
        $creator = new UseCaseCreateInteractorCreator($command->folder, $command->fileName, $command->properties);
        $creator->run();
    }

}
