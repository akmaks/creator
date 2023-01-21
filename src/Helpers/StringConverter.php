<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\Helpers;

class StringConverter
{
    /**
     * @param string $string
     * @return string
     */
    public function snakeCaseToCamelCase(string $string): string
    {
        $words = explode('_', $string);
        $words = array_map('ucfirst', $words);

        return implode('', $words);
    }
}