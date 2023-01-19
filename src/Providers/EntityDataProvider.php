<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\Providers;

use Akimmaksimov85\CreatorBundle\Entity\Method;
use Akimmaksimov85\CreatorBundle\Entity\Meta;
use Akimmaksimov85\CreatorBundle\Entity\Property;
use Akimmaksimov85\CreatorBundle\Enums\FileTypes;
use Akimmaksimov85\CreatorBundle\Enums\Visibility;

class EntityDataProvider extends AbstractDataProvider
{
    private const ID_TYPE_RAMSEY_UUID = '\\Ramsey\\Uuid\\Uuid';
    private const ID_TYPE_RAMSEY_UUID_INTERFACE = '\\Ramsey\\Uuid\\UuidInterface';

    /**
     * @param array $properties
     * @return Meta
     */
    public function getFilledGenerator(array $properties): Meta
    {
        return $this->meta
            ->setType($this->getFileType())
            ->setUses($this->getUses($properties))
            ->setProperties($this->getProperties($properties))
            ->setMethods($this->getMethods($properties));
    }

    /**
     * @param array $properties
     * @return array
     */
    private function getUses(array $properties): array
    {
        $uses = [];

        foreach ($properties as $property => $type) {
            if ($type === 'string' && $property === 'id') {
                $uses[] = self::ID_TYPE_RAMSEY_UUID;
                $uses[] = self::ID_TYPE_RAMSEY_UUID_INTERFACE;
                continue;
            }

            if (ucfirst($property) === $type) {
                $uses[] = $this->getPropertyType($property, $type);
            }
        }

        return $uses;
    }

    /**
     * @param array $properties
     * @return array
     */
    private function getProperties(array $properties): array
    {
        $providerProperties = [];

        foreach ($properties as $property => $type) {
            if ($type === 'string' && $property === 'id') {
                $type = self::ID_TYPE_RAMSEY_UUID_INTERFACE;
                $providerProperties[] =
                    (new Property())
                        ->setName('id')
                        ->setType($type)
                        ->setVisibility(Visibility::VISIBILITY_PRIVATE->value)
                        ->setComment(
                            $this->contentBuilder->setString("@var UuidInterface")->run()
                        );
                continue;
            }

            $providerProperties[] =
                (new Property())
                    ->setName(lcfirst($property))
                    ->setType($this->getPropertyType($property, $type))
                    ->setVisibility(Visibility::VISIBILITY_PRIVATE->value)
                    ->setComment($this->contentBuilder->setString("@var " . $type)->run());
        }

        return $providerProperties;
    }

    /**
     * @param array $properties
     * @return array
     */
    private function getMethods(array $properties): array
    {
        $methods = [];

        foreach ($properties as $property => $type) {
            if ($property === 'id' && $type === 'string') {
                $methods[] = $this->makeConstructorWithUuid();
            } else {
                $methods[] = $this->makeSetter($property, $type);
            }

            $methods[] = $this->makeGetter($property, $type);
        }

        return $methods;
    }

    /**
     * Method makes getter
     *
     * @param string $property
     * @param string $type
     *
     * @return Method
     */
    private function makeGetter(string $property, string $type): Method
    {
        return (new Method())
            ->setName(sprintf("get%s", ucfirst($property)))
            ->setComment(
                sprintf(
                    "Method returns %s of %s \n\n@return %s",
                    $property,
                    $this->meta->getFileName(),
                    $type
                )
            )
            ->setVisibility(Visibility::VISIBILITY_PUBLIC->value)
            ->setBody(
                $this
                    ->contentBuilder
                    ->setString(
                        sprintf(
                            "return \$this->%s%s;",
                            $property,
                            $property === 'id' && $type === 'string' ? '->toString()' : '',
                        )
                    )
                    ->run()
            )
            ->setReturn($type);
    }

    /**
     * @param string $property
     * @param string $type
     * @return Method
     */
    private function makeSetter(string $property, string $type): Method
    {
        return (new Method())
            ->setName(sprintf("set%s", ucfirst($property)))
            ->setComment(
                sprintf(
                    "Method sets %s of %s. \n\n@param %s \$%s %s \n\n@return void",
                    $property,
                    $this->meta->getFileName(),
                    $type,
                    $property,
                    ucfirst($property)
                )
            )
            ->setProperties(
                [
                    $property => $type,
                ]
            )
            ->setVisibility(Visibility::VISIBILITY_PUBLIC->value)
            ->setBody(
                $this
                    ->contentBuilder
                    ->setString(sprintf("\$this->%s = \$%s;", $property, $property))
                    ->run()
            )
            ->setReturn('void');
    }

    /**
     * @return Method
     */
    private function makeConstructorWithUuid(): Method
    {
        return (new Method())
            ->setName('__construct')
            ->setComment(
                sprintf("%s Constructor.\n\n", $this->meta->getFileName())
            )
            ->setVisibility(Visibility::VISIBILITY_PUBLIC->value)
            ->setBody("\$this->id = Uuid::uuid4();\n");
    }

    /**
     * @param string $property
     * @param string $type
     * @return string
     */
    private function getPropertyType(string $property, string $type): string
    {
        if (ucfirst($property) !== $type) {
            return $type;
        }

        return sprintf(
            '%s%s\\%s',
            rtrim($this->meta->getNamespacePath(), $this->meta->getEntityName()),
            $type,
            $type
        );
    }

    /**
     * @return string
     */
    private function getFileType(): string
    {
        return FileTypes::FILE_TYPE_CLASS->value;
    }
}