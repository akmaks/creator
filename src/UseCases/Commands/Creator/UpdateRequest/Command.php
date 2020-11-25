<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\UseCases\Commands\Creator\UpdateRequest;

use Akimmaksimov85\CreatorBundle\UseCases\Commands\AbstractCommand;
use Symfony\Component\Validator\Constraints as Assert;

class Command extends AbstractCommand
{
    /**
     * Folder
     *
     * @Assert\Type("string")
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $folder;

    /**
     * File name
     *
     * @Assert\Type("string")
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $fileName;

    /**
     * File properties
     *
     * @Assert\Type("array")
     * @Assert\NotBlank()
     *
     * @var array
     */
    public $properties;
}
