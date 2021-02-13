<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

/**
 * Class GatewayInterfaceGenerator
 *
 * @package App\Infrastructure\CodeGenerators
 */
class GatewayInterfaceCreator extends AbstractCreator
{
    const FILE_NAME_POSTFIX_GATEWAY_INTERFACE = 'GatewayInterface';

    /**
     * @var
     */
    protected $entityName;

    /**
     * GatewayInterfaceCreator constructor.
     *
     * @param string $folderPath
     * @param string $entityName
     * @param string $type
     */
    public function __construct(
        string $folderPath,
        string $entityName,
        string $type = self::FILE_TYPE_INTERFACE
    )
    {
        $this->entityName = $entityName;

        parent::__construct($folderPath, $this->makeFileName($entityName), $type);
    }

    /**
     *
     */
    protected function initUses(): void
    {
        $this->uses[] = 'App\\Entities\\Entity';
        $this->uses[] = 'Doctrine\\Common\\Collections\\ArrayCollection';
    }

    /**
     * @return string
     */
    protected function getEntityName(): string
    {
        return $this->entityName;
    }

    /**
     * @param string $entity
     *
     * @return string
     */
    protected function makeFileName(string $entity)
    {
        return $entity . self::FILE_NAME_POSTFIX_GATEWAY_INTERFACE;
    }

    /**
     * @return void
     */
    protected function initMethods(): void
    {
        $this->methods = [
            'getById' => [
                'comment' => sprintf(
                    "Get information about specific %s. \n\n@param %s $%s %s ID \n\n@return null|%s|%s",
                    $this->getEntityName(),
                    'string',
                    'id',
                    $this->getEntityName(),
                    $this->getEntityName(),
                    'Entity'
                ),
                'visibility' => 'public',
                'parameters' => [
                    'id' => 'string'
                ],
                'return' => '?Entity',
            ],
            'delete' => [
                'comment' => sprintf(
                    "Delete specific %s. \n\n@param %s $%s %s \n\n@return void",
                    $this->getEntityName(),
                    'Entity',
                    'entity',
                    lcfirst($this->getEntityName())
                ),
                'visibility' => 'public',
                'parameters' => [
                    'entity' => 'Entity'
                ],
                'return' => 'void',
            ],
            'create' => [
                'comment' => sprintf(
                    "Create new %s. \n\n@param %s|%s $%s %s \n\n@return %s|%s",
                    $this->getEntityName(),
                    'Entity',
                    $this->getEntityName(),
                    'entity',
                    $this->getEntityName(),
                    $this->getEntityName(),
                    'Entity'
                ),
                'visibility' => 'public',
                'parameters' => [
                    'entity' => 'Entity'
                ],
                'return' => 'Entity',
            ],
            'update' => [
                'comment' => sprintf(
                    "Update %s. \n\n@param %s|%s $%s %s \n\n@return %s|%s",
                    $this->getEntityName(),
                    'Entity',
                    $this->getEntityName(),
                    'entity',
                    $this->getEntityName(),
                    $this->getEntityName(),
                    'Entity'
                ),
                'visibility' => 'public',
                'parameters' => [
                    'entity' => 'Entity'
                ],
                'return' => 'Entity',
            ],
        ];
    }
}
