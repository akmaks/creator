<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\Command;

use Akimmaksimov85\CreatorBundle\Builders\ContentBuilder\ContentBuilder;
use Akimmaksimov85\CreatorBundle\Builders\PhpFileBuilder\PhpFileBuilder;
use Akimmaksimov85\CreatorBundle\Entity\Meta;
use Akimmaksimov85\CreatorBundle\Exceptions\InvalidInputFileFormatException;
use Akimmaksimov85\CreatorBundle\Exceptions\InvalidInputPropertiesFormatException;
use Akimmaksimov85\CreatorBundle\Helpers\StringConverter;
use Akimmaksimov85\CreatorBundle\Providers\AbstractDataProvider;
use Akimmaksimov85\CreatorBundle\Providers\DataProviderFactory;
use Akimmaksimov85\CreatorBundle\UseCases\Commands\Create\Interactor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

abstract class AbstractGenerateCommand extends Command
{
    /**
     * @var string
     */
    protected string $folderPath;

    /**
     * @var string
     */
    protected string $fileName;

    /**
     * @var array
     */
    protected array $properties;

    public function __construct(
        protected readonly PhpFileBuilder $phpFileBuilder,
        protected readonly ContentBuilder $contentBuilder,
        protected readonly StringConverter $stringConverter,
        string    $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * @param \Akimmaksimov85\CreatorBundle\Providers\AbstractDataProvider $dataProvider
     * @param array $properties
     * @return int
     */
    public function runInteractor(AbstractDataProvider $dataProvider, array $properties): int
    {
        $interactor = new Interactor($this->phpFileBuilder, $dataProvider);

        $command = new \Akimmaksimov85\CreatorBundle\UseCases\Commands\Create\Command();
        $command->properties = $properties;

        $interactor($command);

        return 0;
    }

    /**
     * @param InputInterface $input
     * @return Meta
     */
    protected function getMeta(InputInterface $input): Meta
    {
        $this->parseData($input->getOptions()['file'], $input->getOptions()['properties']);

        return new Meta($this->fileName, $this->folderPath, $this->fileName);
    }

    /**
     * @param string|null $fileData
     * @param string|null $propertiesData
     *
     * @return void
     */
    private function parseData(string $fileData = null, string $propertiesData = null): void
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

    /**
     * @param InputInterface $input
     * @return DataProviderFactory
     */
    protected function getDataProviderFactory(InputInterface $input): DataProviderFactory
    {
        return new DataProviderFactory(
            $this->getMeta($input),
            $this->contentBuilder,
            $this->stringConverter
        );
    }
}