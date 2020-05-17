<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

/**
 * Class DoctrineGatewayCreator
 * @package Akimmaksimov85\CreatorBundle\Entity
 */
class DoctrineGatewayCreator extends AbstractCreator
{
    const FILE_NAME_POSTFIX_ENTITY = 'Entity';
    const FILE_NAME_POSTFIX_ENTITY_GATEWAY = 'Gateway';

    /**
     * @var string
     */
    protected $entityName;

    /**
     * EntityCreator constructor.
     *
     * @param string $folderPath
     * @param string $fileName
     * @param array $properties
     */
    public function __construct(string $folderPath, string $fileName, array $properties = [])
    {
        $this->entityName = $fileName;

        parent::__construct($folderPath, $this->makeFileName($fileName));
    }

    /**
     * @return string
     */
    protected function getExtend(): string
    {
        return 'App\\Data\\Gateways\\Doctrine\\DoctrineCrudGateway';
    }

    /**
     *
     */
    protected function initImplements(): void
    {
        $this->implements[] = sprintf(
            'App\\Entities\\%s\\%sGatewayInterface',
            $this->entityName,
            $this->entityName
        );
    }

    /**
     *
     */
    protected function initMethods(): void
    {
        $this->methods = array_merge(
            $this->methods,
            $this->makeGetDoctrineEntityName($this->entityName)
        );
    }

    /**
     * @param string $entity
     * @return array
     */
    protected function makeGetDoctrineEntityName(string $entity): array
    {
        return [
            'getDoctrineEntityName' => [
                'comment' => "Method returns doctrine entity name \n\n@return string",
                'visibility' => 'protected',
                'return' => 'string',
                'body' => sprintf("return %s::class;", $entity . self::FILE_NAME_POSTFIX_ENTITY)
            ]
        ];
    }

    /**
     * @param string $entity
     *
     * @return string
     */
    protected function makeFileName(string $entity)
    {
        return ucfirst(mb_strtolower($entity)) . self::FILE_NAME_POSTFIX_ENTITY_GATEWAY;
    }
}