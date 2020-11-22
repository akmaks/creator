<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

/**
 * Class EntityDTOCreator
 * @package Akimmaksimov85\Entity
 */
class UseCaseUpdateInteractorCreator extends UseCaseDefaultInteractorCreator
{
    /**
     *
     */
    protected function initUses(): void
    {
        $this->uses[] = sprintf(
            'App\\Data\\Exceptions\\%s\\%sNotFoundByIdException',
            ucfirst($this->entityName),
            ucfirst($this->entityName)
        );
        $this->uses[] = 'App\\Entities\\AbstractDTO';
        $this->uses[] = sprintf(
            'App\\Entities\\%s\\%sDTO',
            ucfirst($this->entityName),
            ucfirst($this->entityName)
        );
        $this->uses[] = sprintf(
            'App\\Entities\\%s\\%sGatewayInterface',
            ucfirst($this->entityName),
            ucfirst($this->entityName)
        );
    }

    /**
     * @return array
     */
    protected function makeInvoke()
    {
        return [
            '__invoke' => [
                'comment' => implode(
                    "",
                    [
                        "Command handler\n\n",
                        "@param Command \$command Command\n\n",
                        sprintf("@return AbstractDTO|%sDTO", ucfirst($this->entityName))
                    ]
                ),
                'visibility' => 'public',
                'return' => '',
                'parameters' => [lcfirst(self::PROPERTY_NAME_COMMAND) => self::PROPERTY_NAME_COMMAND],
                'body' => implode(
                    "",
                    array_merge(
                        [
                            sprintf(
                                "if (empty(\$%s = \$this->%s->getById(\$command->id)) === true) {\n",
                                lcfirst($this->entityName),
                                lcfirst($this->getGatewayName())
                            )
                        ],
                        [
                            sprintf(
                                "    throw new %sNotFoundByIdException(\$command->id);\n}\n\n",
                                ucfirst($this->entityName)
                            )
                        ],
                        array_map(
                            function (string $property) {
                                if ($property === 'id') {
                                    return '';
                                }

                                return sprintf(
                                    "\$%s->set%s(\$command->%s);\n",
                                    lcfirst($this->entityName),
                                    ucfirst($property),
                                    lcfirst($property)
                                );
                            },
                            array_keys($this->properties)
                        ),
                        [
                            "\n"
                        ],
                        [
                            sprintf(
                                "\$%s = \$this->%s->update(\$%s);\n\n",
                                lcfirst($this->entityName),
                                lcfirst($this->getGatewayName()),
                                lcfirst($this->entityName)
                            )
                        ],
                        [
                            sprintf(
                                "return \$%s->toDTO();",
                                lcfirst($this->entityName)
                            )
                        ]
                    )
                ),
            ],
        ];
    }
}