<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\Providers;

use Akimmaksimov85\CreatorBundle\Entity\Meta;
use Akimmaksimov85\CreatorBundle\Enums\FileTypes;

class EntityDoctrineRepositoryProvider extends AbstractDataProvider
{
    public const FILE_NAME_POSTFIX = 'Repository';

    /**
     * @param array $properties
     * @return Meta
     */
    public function getFilledGenerator(array $properties): Meta
    {
        return $this->getMeta()
            ->setType($this->getFileType())
            ->setFileName($this->getMeta()->getFileName() . self::FILE_NAME_POSTFIX)
            ->setFileComment($this->getFileComment())
            ->setUses($this->getUses())
            ->setExtends('Doctrine\\ORM\\EntityRepository');
    }

    /**
     * @return string[]
     */
    private function getUses(): array
    {
        return [
            'Doctrine\\ORM\\EntityRepository',
        ];
    }

    /**
     * @return string
     */
    private function getFileComment(): string
    {
        return $this->getContentBuilder()
            ->setString($this->getMeta()->getFileName(), "\n\n")
            ->setString(
                sprintf(
                    '@method %s|null find($id, $lockMode = null, $lockVersion = null)',
                    $this->getMeta()->getEntityName()
                )
            )
            ->setString(
                sprintf(
                    '@method %s[]    findAll()',
                    $this->getMeta()->getEntityName()
                )
            )
            ->setString(
                sprintf(
                    '@method %s[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)',
                    $this->getMeta()->getEntityName()
                )
            )
            ->setString(
                sprintf(
                    '@method %s|null findOneBy(array $criteria, array $orderBy = null)',
                    $this->getMeta()->getEntityName()
                )
            )
            ->run();
    }

    /**
     * @return string
     */
    private function getFileType(): string
    {
        return FileTypes::FILE_TYPE_CLASS->value;
    }
}