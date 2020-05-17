<?php
/**
 * Domain exception: не найден бренд
 *
 * @author Irina Volosevich <i.volosevich@artox.com>
 */

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\Exceptions;

class InvalidInputPropertiesFormatException extends \DomainException
{

    /**
     * InvalidInputPropertiesFormatException constructor.
     */
    public function __construct()
    {
        parent::__construct('Invalid input properties format. Valid format: {propertyName}:{propertyType}/{propertyName}:{propertyType}');
    }

}
