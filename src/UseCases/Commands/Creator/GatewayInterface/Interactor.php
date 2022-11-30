<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\GatewayInterface;

use Akimmaksimov85\CreatorBundle\UseCases\Commands\AbstractInteractor;
use Akimmaksimov85\CreatorBundle\Entity\GatewayInterfaceCreator;

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
        $creator = new GatewayInterfaceCreator($command->folder, $command->entity, $command->properties);
        $creator->run();
    }

}
