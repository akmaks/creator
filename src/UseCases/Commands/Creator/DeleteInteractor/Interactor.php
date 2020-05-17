<?php

namespace Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DeleteInteractor;

use Akimmaksimov85\CreatorBundle\Entity\UseCaseDeleteInteractorCreator;
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
        $creator = new UseCaseDeleteInteractorCreator($command->folder, $command->fileName, $command->properties);
        $creator->run();
    }

}
