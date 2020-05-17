<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\DoctrineEntityRepository;

use Akimmaksimov85\CreatorBundle\Entity\DoctrineEntityCreator;
use Akimmaksimov85\CreatorBundle\Entity\DoctrineEntityRepositoryCreator;
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
        $creator = new DoctrineEntityRepositoryCreator($command->folder, $command->fileName, $command->properties);
        $creator->run();
    }

}
