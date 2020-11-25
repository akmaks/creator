<?php


namespace Akimmaksimov85\CreatorBundle\Entity;

/**
 * Class UpdateRequestCreator
 * @package Akimmaksimov85\CreatorBundle\Entity
 */
class UpdateRequestCreator extends AbstractCreator
{
    /**
     * @var array
     */
    protected $params;

    /**
     * @var string
     */
    protected $entity;

    /**
     * CreateRequestCreator constructor.
     *
     * @param string $folderPath
     * @param string $fileName
     * @param array $properties
     */
    public function __construct(string $folderPath, string $fileName, array $properties = [])
    {
        $this->entity = $fileName;
        $this->params = $properties;

        parent::__construct($folderPath, $this->makeFileName());
    }

    /**
     * @return string
     */
    protected function makeFileName()
    {
        return 'UpdateRequest';
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
                'body' => $this->getRulesReturn($this->params),
            ]
        ];

        foreach ($this->params as $property => $type) {
            $this->methods = array_merge(
                $this->methods,
                [
                    $property => [
                        'comment' => sprintf(
                            "%s %s \n\n@return %s",
                            $this->entity,
                            $property,
                            $type
                        ),
                        'visibility' => 'public',
                        'return' => $type,
                        'body' => sprintf(
                            implode(
                                "",
                                [
                                    "return (%s) \$this->params['%s'];",
                                ]
                            ),
                            $type,
                            $property
                        ),
                    ]
                ]
            );
        }

        parent::initMethods();
    }

    /**
     * @param array $properties
     * @return string
     */
    protected function getRulesReturn(array $properties)
    {
        $body = "return [";

        foreach ($properties as $property => $type) {
            $body .= sprintf(
                implode(
                    "",
                    [
                        "\n    '%s' => [\n",
                        "        new Assert\Type(\"string\"),\n",
                        "        new Assert\NotBlank(),\n",
                        "    ],\n"
                    ]
                ),
                $property
            );
        }

        $body .= "];";

        return $body;
    }
}