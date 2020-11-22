<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

/**
 * Class EntityDTOCreator
 * @package Akimmaksimov85\Entity
 */
class EntityDTOCreator extends AbstractCreator
{
    const FILE_NAME_POSTFIX_DTO = 'DTO';

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
        $this->entityName = $fileName;
        $this->properties = $properties;

        parent::__construct($folderPath, $this->makeFileName($fileName));
    }

    /**
     * @return string
     */
    protected function getExtend(): string
    {
        return 'App\\Entities\\AbstractDTO';
    }

    /**
     * @param string $entity
     *
     * @return string
     */
    protected function makeFileName(string $entity)
    {
        return $entity . self::FILE_NAME_POSTFIX_DTO;
    }

    /**
     * @return void
     */
    protected function initProperties(): void
    {
        $properties = [];

        foreach ($this->properties as $property => $type) {

            if (ucfirst($type) === $type) {
                $typeDTO = $type . self::FILE_NAME_POSTFIX_DTO;
                $this->uses[] =  sprintf('App\\Entities\\%s\\%s', $type, $typeDTO);
                $type = $typeDTO;
            }

            $properties[lcfirst($property)] = [
                'comment' => sprintf("%s %s \n\n@var %s", $this->entityName, $property, $type),
                'visibility' => 'public',
            ];
        }

        $this->properties = $properties;
    }
}