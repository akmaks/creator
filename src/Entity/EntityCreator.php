<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

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
     * @param string $folderPath
     * @param string $fileName
     * @param array $properties
     */
    public function __construct(string $folderPath, string $fileName, array $properties = [])
    {
        foreach ($properties as $property => $type) {
            if ($type === 'string' && $property === 'id') {
                $this->uses[] = 'Ramsey\\Uuid\\Uuid';
                $this->uses[] = 'Ramsey\\Uuid\\UuidInterface';
                $this->properties['id'] = 'UuidInterface';
                continue;
            }

            if (ucfirst($property) === $property) {
                $this->uses[] = sprintf('App\\Entities\\%s\\%s', $property, $property);
            }

            $this->properties[lcfirst($property)] = $type;
        }

        parent::__construct($folderPath, $fileName);
    }

    /**
     *
     */
    protected function initImplements(): void
    {
    }

    /**
     *
     */
    protected function initTraits(): void
    {
        $this->traits[] = 'App\\Entities\\Traits\\CreatedUpdatedFieldsTrait';
    }

    /**
     * @return void
     */
    protected function initMethods(): void
    {
        $methods = [];

        foreach ($this->properties as $property => $type) {
            $methods = array_merge($methods, $this->makeGetter($property, $type));

            if ($property === 'id') {
                if ($type === 'UuidInterface') {
                    $methods = array_merge($methods, $this->makeConstructor());
                }
                continue;
            }

            $methods = array_merge($methods, $this->makeSetter($property, $type));
        }

        $this->methods = $methods;
    }

    /**
     * @return void
     */
    protected function initProperties(): void
    {
        $properties = [];

        foreach ($this->properties as $property => $type) {
            $properties[$property] = [
                'comment' => sprintf(
                    implode(
                        "",
                        [
                            "%s %s\n\n",
                            "@var %s%s"
                        ]
                    ),
                    $this->getFileName(),
                    $property,
                    $type,
                    $this->getIfIdNullableComment($property)
                ),
                'visibility' => 'private',
                'type' => $type,
            ];
        }

        $this->properties = $properties;
    }

    /**
     * @param string $property
     *
     * @return string
     */
    protected function getIfIdNullableComment(string $property): string
    {
        return '';
    }

    /**
     * @param string $property
     *
     * @return string
     */
    protected function getIfIdNullableProperty(string $property): string
    {
        return '';
    }

    /**
     * Method makes getter
     *
     * @param string $property
     * @param string $type
     *
     * @return array
     */
    protected function makeGetter(string $property, string $type)
    {
        return [
            sprintf("get%s", ucfirst($property)) => [
                'comment' => sprintf(
                    "Method returns %s of %s \n\n@return %s",
                    $property,
                    $this->getFileName(),
                    $type === 'UuidInterface' ? 'string' : $type
                ),
                'visibility' => 'public',
                'return' => $type === 'UuidInterface' ? 'string' : $type,
                'body' => sprintf(
                    "return \$this->%s%s;",
                    $property,
                    $type === 'UuidInterface' ? '->toString()' : '',
                )
            ]
        ];
    }

    /**
     * Method makes setter
     *
     * @param string $property
     * @param string $type
     *
     * @return array
     */
    protected function makeSetter(string $property, string $type)
    {
        return [
            sprintf("set%s", ucfirst($property)) => [
                'comment' => sprintf(
                    "Method sets %s of %s. \n\n@param %s \$%s %s \n\n@return void",
                    $property,
                    $this->getFileName(),
                    $type,
                    $property,
                    ucfirst($property)
                ),
                'parameters' => [
                    $property => $type
                ],
                'visibility' => 'public',
                'return' => 'void',
                'body' => sprintf("\$this->%s = \$%s;", $property, $property)
            ]
        ];
    }

    /**
     * Method makes constructor
     *
     * @return array
     */
    protected function makeConstructor(): array
    {
        return [
            '__construct' => [
                'comment' => sprintf("%s Constructor.\n\n", $this->getFileName()),
                'visibility' => 'public',
                'return' => '',
                'body' => "\$this->id = Uuid::uuid4();\n",
            ],
        ];
    }

    /**
     * Make toDTO method
     *
     * @param string $entityName Entity name
     *
     * @return array
     */
    protected function makeToDTO(string $entityName)
    {
        return [
            'toDTO' => [
                'comment' => sprintf(
                    "Make DTO from entity \n\n@return %s|AbstractDTO",
                    $entityName . 'DTO'
                ),
                'parameters' => [],
                'visibility' => 'public',
                'return' => 'AbstractDTO',
                'body' => implode(
                    "",
                    array_merge(
                        [
                            sprintf(
                                "\$dto = new %sDTO();\n",
                                ucfirst($entityName)
                            )
                        ],
                        array_map(
                            function (string $property) {
                                return sprintf(
                                    "\$dto->%s = \$this->get%s();\n",
                                    lcfirst($property),
                                    ucfirst($property)
                                );
                            },
                            array_keys($this->properties)
                        ),
                        [
                            "\n"
                        ],
                        [
                            "return \$dto;"
                        ]
                    )
                ),
            ]
        ];
    }

    /**
     * @return string
     */
    protected function getClassComment(): string
    {
        return $this->type . ' ' . $this->getFileName();
    }
}