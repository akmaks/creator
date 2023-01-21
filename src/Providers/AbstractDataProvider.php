<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\Providers;

use Akimmaksimov85\CreatorBundle\Builders\ContentBuilder\ContentBuilder;
use Akimmaksimov85\CreatorBundle\Entity\Meta;
use Akimmaksimov85\CreatorBundle\Helpers\StringConverter;

abstract class AbstractDataProvider
{
    public function __construct(
        private readonly Meta $meta,
        private readonly ContentBuilder $contentBuilder,
        private readonly StringConverter $stringConverter
    ) {
    }

    /**
     * @param array $properties
     * @return Meta
     */
    abstract function getFilledGenerator(array $properties): Meta;

    /**
     * @return Meta
     */
    protected function getMeta(): Meta
    {
        return $this->meta;
    }

    /**
     * @return ContentBuilder
     */
    protected function getContentBuilder(): ContentBuilder
    {
        return $this->contentBuilder;
    }

    /**
     * @return StringConverter
     */
    protected function getStringConverter(): StringConverter
    {
        return $this->stringConverter;
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getObjectPropertyType(string $type): string
    {
        return sprintf(
            '%s%s\\%s',
            rtrim($this->getMeta()->getNamespacePath(), $this->getMeta()->getEntityName()),
            $type,
            $type
        );
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    protected function isPropertyObject(string $type): bool
    {
        return ucfirst($type) === $type;
    }
}