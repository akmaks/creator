<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

/**
 * Class EntityCreator
 * @package Akimmaksimov85\Entity
 */
class DoctrineEntityRepositoryCreator extends AbstractCreator
{
    const FILE_NAME_POSTFIX_ENTITY = 'Entity';
    const FILE_NAME_POSTFIX_ENTITY_REPOSITORY = 'EntityRepository';

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

    protected function getExtend(): string
    {
        return 'Doctrine\\ORM\\EntityRepository';
    }

    /**
     * @return string
     */
    protected function getFileComment(): string
    {
        return implode(
            "",
            [
                "\n",
                sprintf(
                    "@method %s|null find(\$id, \$lockMode = null, \$lockVersion = null)\n",
                    $this->entityName . self::FILE_NAME_POSTFIX_ENTITY
                ),
                sprintf(
                    "@method %s[]    findAll()\n",
                    $this->entityName . self::FILE_NAME_POSTFIX_ENTITY
                ),
                sprintf(
                    "@method %s[]    findBy(array \$criteria, array \$orderBy = null, \$limit = null, \$offset = null)\n",
                    $this->entityName . self::FILE_NAME_POSTFIX_ENTITY
                ),
                sprintf(
                    "@method %s|null findOneBy(array \$criteria, array \$orderBy = null)\n",
                    $this->entityName . self::FILE_NAME_POSTFIX_ENTITY
                ),
            ]
        );
    }

    /**
     * @param string $entity
     *
     * @return string
     */
    protected function makeFileName(string $entity)
    {
        return ucfirst(mb_strtolower($entity)) . self::FILE_NAME_POSTFIX_ENTITY_REPOSITORY;
    }
}