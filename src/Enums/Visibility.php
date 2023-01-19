<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\Enums;

enum Visibility: string
{
    case VISIBILITY_PRIVATE = 'private';
    case VISIBILITY_PROTECTED = 'protected';
    case VISIBILITY_PUBLIC = 'public';
}