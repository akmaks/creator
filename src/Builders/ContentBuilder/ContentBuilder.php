<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\Builders\ContentBuilder;

class ContentBuilder
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
    public function setString(string $string, string $separator = "\n"): ContentBuilder
    {
        $this->strings[] = $string;
        $this->strings[] = $separator;

        return $this;
    }

    /**
     * @return string
     */
    public function run(): string
    {
        return implode("", $this->shiftStrings());
    }

    /**
     * @return array
     */
    protected function shiftStrings(): array
    {
        $strings = $this->strings;

        $this->strings = [];

        return $strings;
    }
}