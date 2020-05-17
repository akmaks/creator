<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

/**
 * Class EntityDTOCreator
 * @package Akimmaksimov85\Entity
 */
class UseCaseDeleteInteractorCreator extends UseCaseDefaultInteractorCreator
{
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
                            "    return;\n}\n\n",
                        ],
                        [
                            sprintf(
                                "\$this->%s->delete(\$%s);\n\n",
                                lcfirst($this->getGatewayName()),
                                lcfirst($this->entityName)
                            )
                        ]
                    )
                ),
            ],
        ];
    }
}