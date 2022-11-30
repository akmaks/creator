<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\Entity;

class CodeBuilder
{
    /**
     * @var array
     */
    private array $strings = [];

    /**
     * @param string $string
     * @param string $separator
     * @return $this
     */
    public function setString(string $string, string $separator = "\n"): CodeBuilder
    {
        $this->strings[] = $string;
        $this->strings[] = $separator;

        return $this;
    }

    /**
     * @return string
     */
    public function build()
    {
        return implode("", $this->strings);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->build();
    }
}