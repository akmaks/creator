<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\Enums;

enum FileTypes: string
{
    case FILE_TYPE_CLASS = 'Class';
    case FILE_TYPE_INTERFACE = 'Interface';
    case FILE_TYPE_TRAIT = 'Trait';

    /**
     * @return array
     */
    public static function getValues(): array
    {
        return array_map(
            static function (FileTypes $fileType) {
                return $fileType->value;
            },
            self::cases()
        );
    }
}