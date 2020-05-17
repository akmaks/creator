<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

/**
 * Class GatewayInterfaceGenerator
 *
 * @package App\Infrastructure\CodeGenerators
 */
class GatewayInterfaceCreator extends AbstractCreator
{
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
        return ucfirst(mb_strtolower($entity)) . 'GatewayInterface';
    }

    /**
     * @return void
     */
    protected function initMethods(): void
    {
        $this->methods = [
            'getAll' => [
                'comment' => sprintf(
                    "Get all %ss. \n\n@param int \$limit Limit\n@param int \$page Page \n\n@return array",
                    lcfirst($this->getEntityName())
                ),
                'visibility' => 'public',
                'parameters' => [
                    'limit' => 'int',
                    'page' => 'int',
                ],
                'return' => 'array',
            ],
            'getById' => [
                'comment' => sprintf(
                    "Get information about specific %s. \n\n@param %s $%s %s ID \n\n@return null|%s|%s",
                    $this->getEntityName(),
                    'int',
                    'id',
                    $this->getEntityName(),
                    $this->getEntityName(),
                    'Entity'
                ),
                'visibility' => 'public',
                'parameters' => [
                    'id' => 'int'
                ],
                'return' => '?Entity',
            ],
            'delete' => [
                'comment' => sprintf(
                    "Delete specific %s. \n\n@param %s $%s %s",
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
            'count' => [
                'comment' => "Returns entities count \n\n@return int",
                'visibility' => 'public',
                'parameters' => [],
                'return' => 'int',
            ],
        ];
    }
}
