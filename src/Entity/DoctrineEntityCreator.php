<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

/**
 * Class EntityCreator
 * @package Akimmaksimov85\Entity
 */
class DoctrineEntityCreator extends AbstractCreator
{
    const FILE_NAME_POSTFIX_ENTITY = 'Entity';
    const ENTITY_PROPERTY_POSTFIX_ID = 'Id';

    /**
     * @var
     */
    protected $entityName;

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * @var array
     */
    protected $relatedEntities = [];

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
                $this->properties[lcfirst($property) . self::ENTITY_PROPERTY_POSTFIX_ID] = 'integer';
                $this->relatedEntities[lcfirst($property) . self::ENTITY_PROPERTY_POSTFIX_ID] = lcfirst($property);
                continue;
            }

            $this->properties[$property] = $type;
        }

        $this->entityName = $fileName;

        parent::__construct($folderPath, $this->makeFileName($fileName));
    }

    protected function getExtend(): string
    {
        return 'App\\Data\\Gateways\\Doctrine\\AbstractDoctrineEntity';
    }

    /**
     * @param string $entity
     *
     * @return string
     */
    protected function makeFileName(string $entity)
    {
        return ucfirst(mb_strtolower($entity)) . self::FILE_NAME_POSTFIX_ENTITY;
    }

    /**
     * @return void
     */
    protected function initUses(): void
    {
        $this->uses[] = 'App\\Entities\\Entity';
        $this->uses[] = 'Doctrine\\ORM\\Mapping as ORM';
        $this->uses[] = 'Symfony\\Component\\Validator\\Constraints as Assert';
        $this->uses[] = sprintf('App\\Entities\\%s\\%s', $this->entityName, $this->entityName);
    }

    /**
     * @return string
     */
    protected function getClassComment(): string
    {
        return implode(
            "",
            [
                sprintf("ORM %s\n\n", $this->getFileName()),
                sprintf("@ORM\Entity(repositoryClass=\"%sRepository\")", $this->getFileName())
            ]
        );
    }

    /**
     * @return void
     */
    protected function initMethods(): void
    {
        $methods = [];

        foreach ($this->properties as $property => $type) {
            $methods = array_merge($methods, $this->makeGetter($property, $type));
        }

        $methods = array_merge(
            $methods,
            $this->makeFromDomainEntity($this->entityName),
            $this->makeToDomainEntity($this->entityName)
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

            if ($type === 'int') {
                $type = 'integer';
            }

            $properties[$property] = [
                'comment' => implode(
                    "",
                    [
                        sprintf("%s\n\n", $property),
                        sprintf("@var %s%s\n\n", $type, $property === 'id' ? "|null" : ""),
                        $property === 'id' ? "@ORM\Id()\n@ORM\GeneratedValue(strategy=\"AUTO\")\n" : "",
                        sprintf("@ORM\Column(type=\"%s\")\n", $type),
                        $property !== 'id' ? "@Assert\NotBlank()\n" : "",
                        $property === 'id' ? "@Assert\Positive()" : "",
                    ]
                ),
                'visibility' => 'private',
            ];
        }

        $this->properties = $properties;
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
                    $type
                ),
                'visibility' => 'public',
                'return' => $type,
                'body' => sprintf("return \$this->%s;", $property)
            ]
        ];
    }

    /**
     * @param string $entity
     *
     * @return array
     */
    protected function makeFromDomainEntity(string $entity): array
    {
        return [
            'fromDomainEntity' => [
                'comment' => implode("",
                    [
                        "Load attributes from domain entity\n\n",
                        sprintf("@param %s|Entity \$%s %s domain entity\n\n", $entity, lcfirst($entity), $entity),
                        "@return void"
                    ]
                ),
                'parameters' => [
                    lcfirst($entity) => 'Entity',
                ],
                'visibility' => 'public',
                'return' => 'void',
                'body' => implode(
                    "",
                    array_map(
                        function (string $property) use ($entity) {
                            return sprintf(
                                "\$this->%s = $%s->get%s();\n",
                                $property,
                                lcfirst($entity),
                                ucfirst($property)
                            );
                        },
                        array_keys($this->properties)
                    )
                )
            ]
        ];
    }

    /**
     * @param string $entity
     *
     * @return array
     */
    protected function makeToDomainEntity(string $entity): array
    {
        return [
            'toDomainEntity' => [
                'comment' => implode("",
                    [
                        "Make domain entity from self state\n\n",
                        sprintf('"@return Entity|%s', ucfirst($entity))
                    ]
                ),
                'visibility' => 'public',
                'return' => 'Entity',
                'body' => implode(
                    "",
                    array_merge(
                        [
                            sprintf("return new %s(\n", ucfirst($entity))
                        ],
                        [
                            rtrim(
                                implode(
                                    "",
                                    array_map(
                                        function (string $property) use ($entity) {
                                            return sprintf("    \$this->%s,\n", $property);
                                        },
                                        array_keys($this->properties)
                                    )
                                ),
                                ",\n"
                            ) . "\n"
                        ],
                        [
                            ');'
                        ]
                    )
                )
            ]
        ];
    }
}