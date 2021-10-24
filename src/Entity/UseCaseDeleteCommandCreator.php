<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

/**
 * Class EntityDTOCreator
 * @package Akimmaksimov85\Entity
 */
class UseCaseDeleteCommandCreator extends AbstractCreator
{
    const FILE_NAME_COMMAND = 'Command';

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

        parent::__construct($folderPath, self::FILE_NAME_COMMAND);
    }

    /**
     * @return string
     */
    protected function getExtend(): string
    {
        return 'App\\UseCases\\AbstractCommand';
    }

    /**
     *
     */
    protected function initUses(): void
    {
        $this->uses[] = 'Symfony\\Component\\Validator\\Constraints';
    }

    /**
     * @return void
     */
    protected function initProperties(): void
    {
        $properties = [];

        foreach ($this->properties as $property => $type) {
            if ($property !== 'id') {
                continue;
            }

            $properties[lcfirst($property)] = [
                'comment' => implode(
                    "",
                    [
                        sprintf("%s %s\n\n", ucfirst($this->entityName), $property),
                        sprintf("@var %s\n\n", $type),
                        sprintf("@Constraints\Type(\"%s\")\n", $type),
                        "@Constraints\NotBlank()"
                    ]
                ),
                'visibility' => 'public',
                'type' => $type,
            ];
        }

        $this->properties = $properties;
    }
}