<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\UseCases\Commands\Create;

class Command
{
    /**
     * File properties
     *
     * @Assert\Type("array")
     * @Assert\NotBlank()
     *
     * @var array
     */
    public array $properties;
}
