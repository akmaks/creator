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
            if (ucfirst($property) === $property) {
                $this->uses[] = sprintf('App\\Entities\\%s\\%s', $property, $property);
            }

            $this->properties[lcfirst($property)] = $type;
        }

        parent::__construct($folderPath, $fileName);

        $this->uses[] = 'App\\Entities\\AbstractDTO';
        $this->uses[] = 'Doctrine\\ORM\\Mapping as ORM';
        $this->uses[] = sprintf(
            'App\\Data\\Gateways\\Doctrine\\%s\\%sRepository',
            $this->getFileName(),
            $this->getFileName()
        );
    }

    /**
     *
     */
    protected function initImplements(): void
    {
        $this->implements[] = 'App\\Entities\\Entity';
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
                continue;
            }

            $methods = array_merge($methods, $this->makeSetter($property, $type));
        }

        $methods = array_merge(
            $methods,
            $this->makeToDTO($this->getFileName())
        );

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
                            $this->getDoctrineAnnotationByProperty($property),
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
    protected function getDoctrineAnnotationByProperty(string $property): string
    {
        switch ($property) {
            case ('id'):
                return implode(
                    "",
                    [
                        "@ORM\Column(type=\"integer\")\n",
                        "@ORM\Id\n",
                        "@ORM\GeneratedValue(strategy=\"AUTO\")\n\n"
                    ]
                );
            case ('name'):
            case ('url'):
            case ('title'):
            case ('password'):
                return "@ORM\Column(type=\"string\", length=255)\n\n";
            default:
                return '';
        }
    }

    /**
     * @param string $property
     *
     * @return string
     */
    protected function getIfIdNullableComment(string $property): string
    {
        return $property === 'id' ? '|null' : '';
    }

    /**
     * @param string $property
     *
     * @return string
     */
    protected function getIfIdNullableProperty(string $property): string
    {
        return $property === 'id' ? '?' : '';
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
                    "Method returns %s of %s \n\n@return %s%s",
                    $property,
                    $this->getFileName(),
                    $type,
                    $this->getIfIdNullableComment($property)
                ),
                'visibility' => 'public',
                'return' => sprintf(
                    '%s%s',
                    $this->getIfIdNullableProperty($property),
                    $type
                ),
                'body' => sprintf("return \$this->%s;", $property)
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
     * @param array $properties
     *
     * @return array
     */
    protected function makeConstructor(array $properties): array
    {
        $body = '';
        $comment = sprintf("%s Constructor.\n\n", $this->getFileName());

        foreach ($properties as $property => $type) {
            $comment .= sprintf(
                "@param %s%s \$%s %s %s\n",
                $type,
                $this->getIfIdNullableComment($property),
                $property,
                $this->getFileName(),
                $property
            );
            $body .= sprintf("\$this->%s = \$%s;\n", $property, $property);
            $properties[$property] = sprintf(
                '%s%s',
                $this->getIfIdNullableProperty($property),
                $type
            );
        }

        return [
            '__construct' => [
                'comment' => $comment,
                'visibility' => 'public',
                'return' => '',
                'parameters' => $properties,
                'body' => $body,
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
        return sprintf(
            implode(
                "\n",
            [
                "%s\n\n@ORM\Table(name=\"%ss\")",
                "@ORM\Entity(repositoryClass=%sRepository::class)",
                "@ORM\HasLifecycleCallbacks",
            ]
            ),
            $this->type . ' ' . $this->getFileName(),
            $this->transformCamelCaseToSnakeCase(lcfirst($this->getFileName())),
            $this->getFileName()
        );
    }
}