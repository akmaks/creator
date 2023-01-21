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
    private const CREATED_AT_UPDATED_AT_TRAIT = 'App\\Entities\\Traits\\CreatedUpdatedFieldsTrait';

    /**
     * @param array $properties
     * @return Meta
     */
    public function getFilledGenerator(array $properties): Meta
    {
        return $this
            ->getMeta()
            ->setType($this->getFileType())
            ->setUses($this->getUses($properties))
            ->setTraits([self::CREATED_AT_UPDATED_AT_TRAIT])
            ->setProperties($this->getProperties($properties))
            ->setMethods($this->getMethods($properties));
    }

    /**
     * @param array $properties
     * @return array
     */
    private function getUses(array $properties): array
    {
        $uses[] = self::CREATED_AT_UPDATED_AT_TRAIT;

        foreach ($properties as $property => $type) {
            if ($type === 'string' && $property === 'id') {
                $uses[] = self::ID_TYPE_RAMSEY_UUID;
                $uses[] = self::ID_TYPE_RAMSEY_UUID_INTERFACE;
                continue;
            }

            if ($this->isPropertyObject($type) && $type !== $this->getMeta()->getEntityName()) {
                $uses[] = $this->getObjectPropertyType($type);
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
                            $this->getContentBuilder()->setString("@var UuidInterface")->run()
                        );
                continue;
            }

            $providerProperties[] =
                (new Property())
                    ->setName(lcfirst($this->getStringConverter()->snakeCaseToCamelCase($property)))
                    ->setType(
                        $this->isPropertyObject($type)
                            ? ''
                            : $type
                    )
                    ->setVisibility(Visibility::VISIBILITY_PRIVATE->value)
                    ->setComment($this->getContentBuilder()->setString("@var " . $type)->run());
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
            ->setName(sprintf("get%s", $this->getStringConverter()->snakeCaseToCamelCase($property)))
            ->setComment(
                sprintf(
                    "Method returns %s of %s \n\n@return %s",
                    lcfirst($this->getStringConverter()->snakeCaseToCamelCase($property)),
                    $this->getMeta()->getFileName(),
                    $this->isPropertyObject($type)
                        ? 'null|' . $type
                        : $type
                )
            )
            ->setVisibility(Visibility::VISIBILITY_PUBLIC->value)
            ->setBody(
                $this
                    ->getContentBuilder()
                    ->setString(
                        sprintf(
                            "return \$this->%s%s;",
                            lcfirst($this->getStringConverter()->snakeCaseToCamelCase($property)),
                            $property === 'id' && $type === 'string' ? '->toString()' : '',
                        )
                    )
                    ->run()
            )
            ->setReturn(
                $this->isPropertyObject($type)
                    ? '?' . $this->getObjectPropertyType($type)
                    : $type
            );
    }

    /**
     * @param string $property
     * @param string $type
     * @return Method
     */
    private function makeSetter(string $property, string $type): Method
    {
        return (new Method())
            ->setName(sprintf("set%s", $this->getStringConverter()->snakeCaseToCamelCase($property)))
            ->setComment(
                sprintf(
                    "Method sets %s of %s. \n\n@param %s \$%s %s \n\n@return %s",
                    lcfirst($this->getStringConverter()->snakeCaseToCamelCase($property)),
                    $this->getMeta()->getFileName(),
                    $this->isPropertyObject($type)
                        ? 'null|' .$type
                        : $type,
                    lcfirst($this->getStringConverter()->snakeCaseToCamelCase($property)),
                    $this->getStringConverter()->snakeCaseToCamelCase($property),
                    'static'
                )
            )
            ->setProperties(
                [
                    lcfirst($this->getStringConverter()->snakeCaseToCamelCase($property)) => $this->isPropertyObject($type)
                        ? '?' . $this->getObjectPropertyType($type)
                        : $type,
                ]
            )
            ->setVisibility(Visibility::VISIBILITY_PUBLIC->value)
            ->setBody(
                $this
                    ->getContentBuilder()
                    ->setString(
                        sprintf(
                            "\$this->%s = \$%s;",
                            lcfirst($this->getStringConverter()->snakeCaseToCamelCase($property)),
                            lcfirst($this->getStringConverter()->snakeCaseToCamelCase($property))
                        ),
                        "\n\n"
                    )
                    ->setString('return $this;')
                    ->run()
            )
            ->setStaticReturn();
    }

    /**
     * @return Method
     */
    private function makeConstructorWithUuid(): Method
    {
        return (new Method())
            ->setName('__construct')
            ->setComment(
                sprintf("%s Constructor.\n\n", $this->getMeta()->getFileName())
            )
            ->setVisibility(Visibility::VISIBILITY_PUBLIC->value)
            ->setBody("\$this->id = Uuid::uuid4();\n");
    }


    /**
     * @return string
     */
    private function getFileType(): string
    {
        return FileTypes::FILE_TYPE_CLASS->value;
    }
}