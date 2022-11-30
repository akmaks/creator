<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

use JetBrains\PhpStorm\ArrayShape;

/**
 * Class GatewayInterfaceGenerator
 *
 * @package App\Infrastructure\CodeGenerators
 */
class GatewayInterfaceCreator extends AbstractCreator
{
    const FILE_NAME_POSTFIX_GATEWAY_INTERFACE = 'GatewayInterface';

    /**
     * @var string
     */
    protected string $entityName;

    /**
     * @param string $folderPath
     * @param string $entityName
     * @param array $properties
     * @param string $type
     */
    public function __construct(
        string $folderPath,
        string $entityName,
        array  $properties = [],
        string $type = self::FILE_TYPE_INTERFACE
    ) {
        $this->entityName = $entityName;
        $this->properties = $properties;

        parent::__construct($folderPath, $this->makeFileName($entityName), $type);
    }

    /**
     *
     */
    protected function initUses(): void
    {
    }

    /**
     * @param string $entity
     *
     * @return string
     */
    protected function makeFileName(string $entity): string
    {
        return $entity . self::FILE_NAME_POSTFIX_GATEWAY_INTERFACE;
    }

    /**
     * @return void
     */
    protected function initMethods(): void
    {
        $this->methods = array_merge(
            $this->methods,
            $this->makeGetById($this->properties, $this->entityName),
            $this->makeCreate($this->entityName),
            $this->makeUpdate($this->entityName),
            $this->makeDelete($this->entityName),
        );
    }

    /**
     * @param array $properties
     * @param string $entityName
     * @return array[]
     */
    #[ArrayShape(['getById' => "array"])]
    protected function makeGetById(array $properties, string $entityName): array
    {
        return [
            'getById' => [
                'comment'    => sprintf(
                    "Find %s by ID\n\n@param %s \$id %s ID\n\n@return null|%s",
                    $entityName,
                    $properties['id'] ?? 'string',
                    $entityName,
                    $entityName
                ),
                'visibility' => 'public',
                'parameters' => [
                    'id' => $properties['id'] ?? 'string',
                ],
                'return'     => '?' . $this->getNamespacePath() . '\\' . $entityName,

            ],
        ];
    }

    /**
     * @param string $entityName
     * @return array
     */
    #[ArrayShape(['delete' => "array"])]
    protected function makeDelete(string $entityName): array
    {
        return [
            'delete' => [
                'comment'    => sprintf(
                    "Delete specific %s. \n\n@param %s $%s %s \n\n@return void",
                    $entityName,
                    $entityName,
                    mb_strtolower($entityName),
                    lcfirst($entityName)
                ),
                'visibility' => 'public',
                'parameters' => [
                    mb_strtolower($entityName) => $entityName,
                ],
                'return'     => 'void',
            ],
        ];
    }

    /**
     * @param string $entityName
     * @return array[]
     */
    #[ArrayShape(['update' => "array"])]
    protected function makeUpdate(string $entityName): array
    {
        return [
            'update' => [
                'comment'    => sprintf(
                    "Update %s. \n\n@param %s $%s %s \n\n@return void",
                    $entityName,
                    $entityName,
                    mb_strtolower($entityName),
                    $entityName
                ),
                'visibility' => 'public',
                'parameters' => [
                    mb_strtolower($entityName) => $entityName,
                ],
                'return'     => 'void',
            ],
        ];
    }

    /**
     * @param string $entityName
     * @return array[]
     */
    #[ArrayShape(['create' => "array"])]
    protected function makeCreate(string $entityName): array
    {
        return [
            'create' => [
                'comment'    => sprintf(
                    "Create new %s. \n\n@param %s $%s %s \n\n@return void",
                    $entityName,
                    $entityName,
                    mb_strtolower($entityName),
                    $entityName
                ),
                'visibility' => 'public',
                'parameters' => [
                    mb_strtolower($entityName) => $entityName,
                ],
                'return'     => 'void',
            ],
        ];
    }
}
