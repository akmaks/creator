<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\Providers;

use Akimmaksimov85\CreatorBundle\Builders\ContentBuilder\ContentBuilder;
use Akimmaksimov85\CreatorBundle\Entity\Meta;
use Akimmaksimov85\CreatorBundle\Helpers\StringConverter;

class DataProviderFactory
{
    public function __construct(
        protected readonly Meta $meta,
        protected readonly ContentBuilder $contentBuilder,
        protected readonly StringConverter $stringConverter
    ) {
    }

    /**
     * @param string $providerName
     * @return AbstractDataProvider
     */
    public function get(string $providerName): AbstractDataProvider
    {
        return match ($providerName) {
            EntityDataProvider::class => new EntityDataProvider(
                $this->meta,
                $this->contentBuilder,
                $this->stringConverter
            ),
            EntityDoctrineGatewayProvider::class => new EntityDoctrineGatewayProvider(
                $this->meta,
                $this->contentBuilder,
                $this->stringConverter
            ),
            EntityDoctrineRepositoryProvider::class => new EntityDoctrineRepositoryProvider(
                $this->meta,
                $this->contentBuilder,
                $this->stringConverter
            ),
            EntityGatewayInterfaceProvider::class => new EntityGatewayInterfaceProvider(
                $this->meta,
                $this->contentBuilder,
                $this->stringConverter
            ),
            default => throw new \DomainException($providerName . ' not found'),
        };
    }
}