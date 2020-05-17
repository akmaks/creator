<?php
/**
 * Domain exception: не найден бренд
 *
 * @author Irina Volosevich <i.volosevich@artox.com>
 */

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\Exceptions;

class InvalidInputFileFormatException extends \DomainException
{

    /**
     * InvalidInputFileFormatException constructor.
     */
    public function __construct()
    {
        parent::__construct('Invalid input file format. Valid format: {EntityFolder}/{EntityFileName}');
    }

}
