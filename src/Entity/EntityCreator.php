<?php

namespace Akimmaksimov85\Entity;

/**
 * Class EntityCreator
 * @package Akimmaksimov85\Entity
 */
class EntityCreator extends AbstractCreator
{
    /**
     * @var array
     */
    protected $properties;

    /**
     * EntityCreator constructor.
     *
     * @param string $domain
     * @param string $fileName
     * @param array $properties
     */
    public function __construct(string $domain, string $fileName, array $properties = [])
    {
        $this->properties = $properties;

        parent::__construct($domain, $fileName, $fileName);
    }

    /**
     * @return array
     */
    protected function getImplements(): array
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getUses(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function getNamespacePath(): string
    {
        return sprintf('App\\Entities\\%s\\%s', $this->getDomain(), $this->getFileName());
    }

    /**
     * @return array
     */
    protected function getMethods(): array
    {
        $methods = [];
        $methods = array_merge($methods, $this->makeConstructor($this->properties));

        foreach ($this->properties as $property => $type) {
            $methods = array_merge($methods, $this->makeGetter($property, $type));

            if ($property === 'id') {
                continue;
            }

            $methods = array_merge($methods, $this->makeSetter($property, $type));
        }

        return $methods;
    }

    /**
     * @return array
     */
    protected function getProperties(): array
    {
        $properties = [];

        foreach ($this->properties as $property => $type) {
            $properties[$property] = [
                'comment' => sprintf("%s %s \n\n@var %s", $this->getFileName(), $property, $type),
                'visibility' => 'protected',
            ];
        }

        return $properties;
    }

    /**
     * @return string
     */
    protected function getFilePath(): string
    {
        return sprintf(
            '/var/www/panel/app/Entities/%s/%s',
            $this->getDomain(),
            $this->getEntity()
        );
    }
}
