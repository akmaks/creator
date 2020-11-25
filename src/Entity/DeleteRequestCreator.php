<?php


namespace Akimmaksimov85\CreatorBundle\Entity;

/**
 * Class DeleteRequestCreator
 * @package Akimmaksimov85\CreatorBundle\Entity
 */
class DeleteRequestCreator extends AbstractCreator
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
        return 'DeleteRequest';
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
            if ($property !== 'id') {
                continue;
            }

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
                            $this->transformCamelCaseToSnakeCase($property)
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
            if ($property !== 'id') {
                continue;
            }

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
                $this->transformCamelCaseToSnakeCase($property)
            );
        }

        $body .= "];";

        return $body;
    }
}