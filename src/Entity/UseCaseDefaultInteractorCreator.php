<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

/**
 * Class EntityDTOCreator
 * @package Akimmaksimov85\Entity
 */
class UseCaseDefaultInteractorCreator extends AbstractCreator
{
    const FILE_NAME_INTERACTOR = 'Interactor';
    const PROPERTY_NAME_GATEWAY = 'Gateway';
    const PROPERTY_NAME_INTERFACE = 'Interface';
    const PROPERTY_NAME_COMMAND = 'Command';

    /**
     * @var string
     */
    protected $entityName;

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
        $this->properties = $properties;
        $this->entityName = $fileName;

        parent::__construct($folderPath, self::FILE_NAME_INTERACTOR);
    }

    /**
     * @return string
     */
    protected function getExtend(): string
    {
        return 'App\\UseCases\\AbstractInteractor';
    }

    /**
     * @return string
     */
    protected function getGatewayInterfaceName(): string
    {
        return ucfirst($this->entityName) . self::PROPERTY_NAME_GATEWAY . self::FILE_TYPE_INTERFACE;
    }

    /**
     * @return string
     */
    protected function getGatewayName(): string
    {
        return ucfirst($this->entityName) . self::PROPERTY_NAME_GATEWAY;
    }

    /**
     * @return void
     */
    protected function initProperties(): void
    {
        $properties[lcfirst($this->getGatewayName())] = [
            'comment' => implode(
                "",
                [
                    sprintf("%s %s\n\n", ucfirst($this->entityName), self::PROPERTY_NAME_GATEWAY),
                    sprintf(
                        "@var %s \$%s",
                        $this->getGatewayInterfaceName(),
                        lcfirst($this->getGatewayName())
                    ),
                ]
            ),
            'visibility' => 'protected',
        ];

        $this->properties = $properties;
    }

    /**
     *
     */
    protected function initUses(): void
    {
        $this->uses[] = sprintf(
            'App\\Entities\\%s\\%sGatewayInterface',
            ucfirst($this->entityName),
            ucfirst($this->entityName)
        );
    }

    /**
     *
     */
    protected function initMethods(): void
    {
        $this->methods = array_merge(
            $this->methods,
            $this->makeConstructor(),
            $this->makeInvoke()
        );
    }

    /**
     * @return array
     */
    protected function makeConstructor(): array
    {
        return [
            '__construct' => [
                'comment' => implode(
                    "",
                    [
                        $this->getFileName() . " constructor\n\n",
                        sprintf(
                            "@param %s \$%s %s",
                            $this->getGatewayInterfaceName(),
                            lcfirst($this->getGatewayName()),
                            $this->entityName . ' ' . self::PROPERTY_NAME_GATEWAY
                        )
                    ]
                ),
                'visibility' => 'public',
                'return' => '',
                'parameters' => [lcfirst($this->getGatewayName()) => $this->getGatewayInterfaceName()],
                'body' => implode(
                    "",
                    [
                        sprintf(
                            "\$this->%s = \$%s;",
                            lcfirst($this->getGatewayName()),
                                lcfirst($this->getGatewayName())
                        )
                    ]
                ),
            ],
        ];
    }

    /**
     * @return array
     */
    protected function makeInvoke()
    {
        return [
            '__invoke' => [
                'comment' => implode(
                    "",
                    [
                        "Command handler\n\n",
                        "@param Command \$command Command\n\n",
                        "@return void\n\n",
                    ]
                ),
                'visibility' => 'public',
                'return' => '',
                'parameters' => [lcfirst(self::PROPERTY_NAME_COMMAND) => self::PROPERTY_NAME_COMMAND],
                'body' => '',
            ],
        ];
    }
}