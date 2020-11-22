<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

/**
 * Class EntityDTOCreator
 * @package Akimmaksimov85\Entity
 */
class UseCaseCreateInteractorCreator extends UseCaseDefaultInteractorCreator
{
    /**
     *
     */
    protected function initUses(): void
    {
        $this->uses[] = 'App\\Entities\\AbstractDTO';
        $this->uses[] = sprintf(
            'App\\Entities\\%s\\%s',
            ucfirst($this->entityName),
            ucfirst($this->entityName)
        );
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
                                "\$%s = new %s();\n",
                                lcfirst($this->entityName),
                                ucfirst($this->entityName)
                            )
                        ],
                        [
                            rtrim(
                                implode(
                                    "",
                                    array_filter(
                                        array_map(
                                            function (string $property) {
                                                if ($property === 'id') {
                                                    return '';
                                                }

                                                return sprintf(
                                                    "    \$%s->set%s(\$command->%s);\n",
                                                    lcfirst($this->entityName),
                                                    ucfirst($property),
                                                    $property
                                                );
                                            },
                                            array_keys($this->properties)
                                        )
                                    )
                                ),
                                ",\n"
                            ) . "\n"
                        ],
                        [
                            sprintf(
                                "\$%s = \$this->%s->create(\$%s);\n\n",
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