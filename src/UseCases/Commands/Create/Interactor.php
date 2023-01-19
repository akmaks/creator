<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\UseCases\Commands\Create;

use Akimmaksimov85\CreatorBundle\Builders\PhpFileBuilder\PhpFileBuilder;
use Akimmaksimov85\CreatorBundle\Providers\AbstractDataProvider;

class Interactor
{
    /**
     * @param PhpFileBuilder $phpFileBuilder
     * @param AbstractDataProvider $dataProvider
     */
    public function __construct(
        private readonly PhpFileBuilder $phpFileBuilder,
        private readonly AbstractDataProvider $dataProvider
    ) {
    }

    /**
     * Command handler
     *
     * @param Command $command Command
     *
     * @return void
     */
    public function __invoke(Command $command): void
    {
        $this->phpFileBuilder->run($this->dataProvider->getFilledGenerator($command->properties));
    }

}
