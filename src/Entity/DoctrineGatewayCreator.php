<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

use JetBrains\PhpStorm\ArrayShape;

/**
 * Class DoctrineGatewayCreator
 * @package Akimmaksimov85\CreatorBundle\Entity
 */
class DoctrineGatewayCreator extends AbstractCreator
{
    protected const FILE_NAME_POSTFIX_ENTITY_GATEWAY = 'Gateway';
    protected const FILE_NAME_POSTFIX_GATEWAY_INTERFACE = 'GatewayInterface';

    /**
     * @var string
     */
    protected string $entityName;

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
        $this->properties = $properties;

        $this->disableAddProperties();

        parent::__construct($folderPath, $this->makeFileName($fileName));
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
            $this->makeConstructor($this->getFileName()),
            $this->makeGetById($this->properties, $this->entityName),
            $this->makeCreate($this->entityName),
            $this->makeUpdate($this->entityName),
            $this->makeDelete($this->entityName),
            $this->makeGetDoctrineEntityName($this->entityName),
        );
    }

    /**
     *
     */
    protected function initUses(): void
    {
        $this->uses[] = sprintf('App\\Entities\\%s\\%s', $this->entityName, $this->entityName);
        $this->uses[] = sprintf(
            'App\\Entities\\%s\\%s',
            $this->entityName,
            $this->entityName . self::FILE_NAME_POSTFIX_GATEWAY_INTERFACE
        );
        $this->uses[] = sprintf('Doctrine\\ORM\\EntityManagerInterface');
    }

    /**
     * Method makes constructor
     *
     * @param string $fileName
     * @return array
     */
    #[ArrayShape(['__construct' => "array"])]
    protected function makeConstructor(string $fileName): array
    {
        return [
            '__construct' => [
                'comment'    => sprintf("%s Constructor.\n\n", $fileName),
                'visibility' => 'public',
                'parameters' => ['em' => 'Doctrine\\ORM\\EntityManagerInterface'],
                'return'     => '',
                'body'       => '',
            ],
        ];
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
                'body'       => $this
                    ->getCodeBuilder()
                    ->setString("\$repository = \$this->em->getRepository(\$this->getEntityName());")
                    ->setString("\$entity     = \$repository->findOneBy(['id' => \$id]);", "\n\n")
                    ->setString(sprintf("if (\$entity instanceof %s) {", $entityName))
                    ->setString("   return \$entity;")
                    ->setString("}", "\n\n")
                    ->setString("return null;"),
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
                'body'       => $this
                    ->getCodeBuilder()
                    ->setString(sprintf("\$this->em->remove(\$%s);", mb_strtolower($entityName)))
                    ->setString("\$this->em->flush();"),
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
                'body'       => $this
                    ->getCodeBuilder()
                    ->setString(sprintf("\$this->em->persist(\$%s);", mb_strtolower($entityName)))
                    ->setString("\$this->em->flush();"),
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
                'body'       => $this
                    ->getCodeBuilder()
                    ->setString(sprintf("\$this->em->persist(\$%s);", mb_strtolower($entityName)))
                    ->setString("\$this->em->flush();"),
                'return'     => 'void',
            ],
        ];
    }

    /**
     * @param string $entity
     * @return array
     */
    #[ArrayShape(['getEntityName' => "array"])]
    protected function makeGetDoctrineEntityName(string $entity): array
    {
        return [
            'getEntityName' => [
                'comment'    => "Method returns doctrine entity name \n\n@return string",
                'visibility' => 'protected',
                'body'       => $this->getCodeBuilder()->setString(sprintf("return %s::class;", $entity)),
                'return'     => 'string',
            ],
        ];
    }

    /**
     * @param string $entity
     *
     * @return string
     */
    protected function makeFileName(string $entity)
    {
        return $entity . self::FILE_NAME_POSTFIX_ENTITY_GATEWAY;
    }
}