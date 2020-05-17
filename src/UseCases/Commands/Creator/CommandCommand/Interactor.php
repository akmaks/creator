<?php

namespace Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\CommandCommand;

use Akimmaksimov85\CreatorBundle\Entity\UseCaseCommandCreator;
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
        $creator = new UseCaseCommandCreator($command->folder, $command->fileName, $command->properties);
        $creator->run();
    }

}
