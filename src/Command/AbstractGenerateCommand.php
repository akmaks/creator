<?php

namespace Akimmaksimov85\CreatorBundle\Command;

use Akimmaksimov85\CreatorBundle\Exceptions\InvalidInputFileFormatException;
use Akimmaksimov85\CreatorBundle\Exceptions\InvalidInputPropertiesFormatException;

abstract class AbstractGenerateCommand extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var string
     */
    protected $folderPath;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var array
     */
    protected $properties;

    /**
     * @param string|null $fileData
     * @param string|null $propertiesData
     *
     * @return void
     */
    protected function parseData(string $fileData = null, string $propertiesData = null): void
    {
        if (empty($fileData) === true) {
            throw new InvalidInputFileFormatException();
        }

        if (strpos($fileData, '/') !== false) {
            $fileData = array_filter(explode('/', $fileData));
            $this->fileName = array_pop($fileData);
            $this->folderPath = implode('/', $fileData);
        } else {
            $this->fileName = $fileData;
            $this->folderPath = '';
        }

        if (isset($propertiesData) === true) {
            $propertiesData = array_filter(explode('/', $propertiesData));
            $this->properties = [];

            foreach ($propertiesData as $propertyData) {
                $propertyData = explode(':', $propertyData);
                $this->properties[array_shift($propertyData)] = array_shift($propertyData);
            }

            if (empty($this->properties) === true) {
                throw new InvalidInputPropertiesFormatException();
            }
        }
    }
}