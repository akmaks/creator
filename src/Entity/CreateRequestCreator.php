<?php


namespace Akimmaksimov85\CreatorBundle\Entity;

/**
 * Class CreateRequestCreator
 * @package Akimmaksimov85\CreatorBundle\Entity
 */
class CreateRequestCreator extends AbstractCreator
{
    /**
     * CreateRequestCreator constructor.
     *
     * @param string $folderPath
     * @param string $fileName
     * @param array $properties
     */
    public function __construct(string $folderPath, string $fileName, array $properties = [])
    {
        foreach ($properties as $property => $type) {
            $this->properties[lcfirst($property)] = $type;
        }

        parent::__construct($folderPath, $fileName);
    }

    /**
     * @param string $entity
     *
     * @return string
     */
    protected function makeFileName(string $entity)
    {
        return 'CreateRequest';
    }

    /**
     *
     */
    protected function initUses(): void
    {
        $this->uses[] = "Symfony\\Component\\Validator\\Constraints as Assert";

        parent::initUses();
    }

    /**
     * @return string
     */
    protected function getExtend(): string
    {
        return 'App\\UI\\AbstractAppRequest';
    }

    /**
     *
     */
    protected function initMethods(): void
    {
        $this->methods = [
            'getRules' => [
                'comment' => sprintf(
                    "Validation rules \n\n@return array"
                ),
                'visibility' => 'public',
                'return' => 'array',
                'body' => $this->getRequestReturn(),
            ]
        ];

        parent::initMethods();
    }

    protected function getRequestReturn()
    {
        return null;
    }
}