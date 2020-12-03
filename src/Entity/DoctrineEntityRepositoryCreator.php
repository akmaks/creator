<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

/**
 * Class EntityCreator
 * @package Akimmaksimov85\Entity
 */
class DoctrineEntityRepositoryCreator extends AbstractCreator
{
    const FILE_NAME_POSTFIX_ENTITY_REPOSITORY = 'Repository';

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
        return 'App\\Data\\Gateways\\Doctrine\\AbstractRepository';
    }

    /**
     *
     */
    protected function initUses(): void
    {
        $this->uses[] = sprintf('App\\Entities\\%s\\%s', $this->entityName, $this->entityName);
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
                    $this->entityName
                ),
                sprintf(
                    "@method %s[]    findAll()\n",
                    $this->entityName
                ),
                sprintf(
                    "@method %s[]    findBy(array \$criteria, array \$orderBy = null, \$limit = null, \$offset = null)\n",
                    $this->entityName
                ),
                sprintf(
                    "@method %s|null findOneBy(array \$criteria, array \$orderBy = null)\n",
                    $this->entityName
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
        return $entity . self::FILE_NAME_POSTFIX_ENTITY_REPOSITORY;
    }
}