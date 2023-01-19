<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

use Akimmaksimov85\CreatorBundle\Enums\FileTypes;

/**
 * Class AbstractCreator
 *
 * @package App\Infrastructure\CodeGenerators
 */
class Meta
{
    /**
     * @var string
     */
    private string $entityName;

    /**
     * @var string
     */
    private string $folderPath;

    /**
     * @var string
     */
    private string $fileName;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var string
     */
    private string $fileComment = '';

    /**
     * @var array
     */
    private array $uses = [];

    /**
     * @var array
     */
    private array $traits = [];

    /**
     * @var string
     */
    private string $extends = '';

    /**
     * @var array
     */
    private array $implements = [];

    /**
     * @var array
     */
    private array $properties = [];

    /**
     * @var array
     */
    private array $methods = [];

    /**
     * @var bool
     */
    private bool $needAddProperties = true;

    /**
     * AbstractCodeGenerator constructor.
     *
     * @param string $entityName
     * @param string $folderPath
     * @param string $fileName
     */
    public function __construct(
        string $entityName,
        string $folderPath,
        string $fileName
    ) {
        $this->entityName = $entityName;
        $this->folderPath = $folderPath;
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return $this->entityName;
    }

    /**
     * @return string
     */
    public function getEntityVarName(): string
    {
        return mb_strtolower($this->entityName);
    }

    /**
     * @return string
     */
    public function getEntityWithNamespace(): string
    {
        return $this->getNamespacePath() . '\\' . $this->getEntityName();
    }

    /**
     * @return string
     */
    public function getFolderPath(): string
    {
        return $this->folderPath;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     * @return $this
     */
    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): static
    {
        $this->type = ucfirst(mb_strtolower($type));

        if (in_array($this->type, $this->getFileTypes()) === false) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid file type %s. Valid types: %s',
                    $this->type,
                    implode(', ', $this->getFileTypes())
                )
            );
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getClassComment(): string
    {
        return $this->type . ' ' . $this->getFileName();
    }

    /**
     * @return string
     */
    public function getNamespacePath(): string
    {
        return str_replace(
            '/',
            '\\',
            'App\\' . $this->getFolderPath()
        );
    }

    /**
     * @return string
     */
    public function getFileComment(): string
    {
        return $this->fileComment;
    }

    /**
     * @param string $fileComment
     * @return $this
     */
    public function setFileComment(string $fileComment): static
    {
        $this->fileComment = $fileComment;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtends(): string
    {
        return $this->extends;
    }

    /**
     * @param string $extend
     * @return $this
     */
    public function setExtends(string $extend): static
    {
        $this->extends = $extend;

        return $this;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     * @return $this
     */
    public function setProperties(array $properties): static
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param array $methods
     * @return $this
     */
    public function setMethods(array $methods): static
    {
        $this->methods = $methods;

        return $this;
    }

    /**
     * @return array
     */
    public function getUses(): array
    {
        return $this->uses;
    }

    /**
     * @param array $uses
     * @return $this
     */
    public function setUses(array $uses): static
    {
        $this->uses = $uses;

        return $this;
    }

    /**
     * @return bool
     */
    public function needAddProperties(): bool
    {
        return $this->needAddProperties;
    }

    /**
     * @param bool $needAddProperties
     * @return $this
     */
    public function setNeedAddProperties(bool $needAddProperties): static
    {
        $this->needAddProperties = $needAddProperties;

        return $this;
    }

    /**
     * @return array
     */
    public function getTraits(): array
    {
        return $this->traits;
    }

    /**
     * @param array $traits
     * @return $this
     */
    public function setTraits(array $traits): static
    {
        $this->traits = $traits;

        return $this;
    }

    /**
     * @return array
     */
    public function getImplements(): array
    {
        return $this->implements;
    }

    /**
     * @param array $implements
     * @return $this
     */
    public function setImplements(array $implements): static
    {
        $this->implements = $implements;

        return $this;
    }

    public function isTypeInterface(): bool
    {
        return $this->type === FileTypes::FILE_TYPE_INTERFACE->value;
    }

    /**
     * @return array
     */
    private function getFileTypes(): array
    {
        return FileTypes::getValues();
    }
}