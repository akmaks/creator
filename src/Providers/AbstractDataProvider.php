<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\Providers;

use Akimmaksimov85\CreatorBundle\Builders\ContentBuilder\ContentBuilder;
use Akimmaksimov85\CreatorBundle\Entity\Meta;

abstract class AbstractDataProvider
{
    public function __construct(
        protected readonly Meta $meta,
        protected readonly ContentBuilder $contentBuilder
    ) {
    }

    /**
     * @param array $properties
     * @return Meta
     */
    abstract function getFilledGenerator(array $properties): Meta;
}