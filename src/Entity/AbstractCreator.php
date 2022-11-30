<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\PsrPrinter;

/**
 * Class AbstractCreator
 *
 * @package App\Infrastructure\CodeGenerators
 */
abstract class AbstractCreator
{
    const FILE_TYPE_CLASS = 'Class';
    const FILE_TYPE_INTERFACE = 'Interface';
    const FILE_TYPE_TRAIT = 'Trait';

    /**
     * @var string
     */
    protected $folderPath;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $uses = [];

    /**
     * @var array
     */
    protected $traits = [];

    /**
     * @var array
     */
    protected $implements = [];

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * @var array
     */
    protected $methods = [];

    /**
     * @var bool
     */
    private bool $addProperties = true;

    /**
     * AbstractCodeGenerator constructor.
     *
     * @param string $folderPath
     * @param string $fileName
     * @param string $type
     */
    public function __construct(
        string $folderPath,
        string $fileName,
        string $type = self::FILE_TYPE_CLASS
    ) {
        $this->folderPath = $folderPath;
        $this->fileName = $fileName;
        $this->type = ucfirst(mb_strtolower($type));

        if (in_array($this->type, $this->getFileTypes()) === false) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Недопустимый тип файла %s. Допустимые типы: %s',
                    $this->type,
                    implode(', ', $this->getFileTypes())
                )
            );
        }

        $this->init();
    }

    /**
     *
     */
    public function init(): void
    {
        $this->initMethods();
        $this->initProperties();
        $this->initImplements();
        $this->initUses();
        $this->initTraits();
    }

    /**
     *
     */
    public function run()
    {
        $file = $this->getFile();
        $file->addComment($this->getFileName());
        $file->setStrictTypes();

        $namespace = $file->addNamespace($this->getNamespacePath());

        foreach ($this->getUses() as $use) {
            $namespace->addUse($use);
        }

        /**
         * @var $class ClassType
         */
        $class = $namespace->{'add' . $this->type}($this->getFileName());
        $class->addComment($this->getClassComment());

        foreach ($this->getImplements() as $implement) {
            $class->addImplement($implement);
        }

        if (empty($this->getExtend()) === false) {
            $class->setExtends($this->getExtend());
            $class->addComment($this->getFileComment());
        }

        foreach ($this->getTraits() as $trait) {
            $class->addTrait($trait);
        }

        if ($this->needAddProperties()) {
            foreach ($this->getProperties() as $property => $attrs) {
                $property = $class->addProperty($property);
                $property->addComment($attrs['comment'] ?? '');
                $property->setType($attrs['type'] ?? '');
                $this->setVisibility($property, $attrs['visibility'] ?? '');
            }
        }

        foreach ($this->getMethods() as $method => $attrs) {
            /**
             * @var $method Method
             */
            $method = $class->addMethod($method);
            $method->addComment($attrs['comment'] ?? '');
            $this->setVisibility($method, $attrs['visibility'] ?? '');
            $method->setReturnType($attrs['return'] ?? '');

            if ($this->type !== self::FILE_TYPE_INTERFACE) {
                $method->setBody($attrs['body'] ?? '');
            }

            foreach (($attrs['parameters'] ?? []) as $parameter => $type) {
                if ($method->getName() !== '__construct') {
                    $method->addParameter($parameter)->setType($type);
                } else {
                    $method->addPromotedParameter($parameter)
                           ->setReadOnly()
                           ->setVisibility('private')
                           ->setType($type);
                }
            }
        }

        if (is_dir($this->getAbsolutePath()) === false) {
            $this->makeDirectory($this->getAbsolutePath());
        }

        file_put_contents(
            $this->getAbsolutePath() . '/' . $this->getFileName() . '.php',
            $this->clearBackSlashes((new PsrPrinter)->printFile($file))
        );
    }

    /**
     * @return string
     */
    protected function getClassComment(): string
    {
        return $this->type . ' ' . $this->getFileName();
    }

    /**
     * @return void
     */
    protected function disableAddProperties(): void
    {
        $this->addProperties = false;
    }

    /**
     * @param string $file
     * @return string
     */
    protected function clearBackSlashes(string $file): string
    {
        $positions = [
            ': \\' => ': ',
            '(\\'  => '(',
            ', \\' => ', ',
        ];

        foreach ($positions as $search => $replace) {
            $file = str_replace($search, $replace, $file);
        }

        return $file;
    }

    /**
     * @param $object
     * @param $visibility
     * @return void
     */
    private function setVisibility($object, $visibility)
    {
        if (empty($visibility) === true) {
            return;
        }

        $setVisibilityMethod = 'set' . ucfirst($visibility);

        if (method_exists($object, $setVisibilityMethod) === false) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Не существует такой области видимости %s. Проверьте конфиги',
                    $visibility
                )
            );
        }

        $object->$setVisibilityMethod();
    }

    /**
     * @return \Nette\PhpGenerator\PhpFile
     */
    protected final function getFile(): PhpFile
    {
        return new PhpFile();
    }

    /**
     * @return string
     */
    protected function getFolderPath(): string
    {
        return $this->folderPath;
    }

    /**
     * @inheritDoc
     */
    protected function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return array
     */
    protected function getFileTypes(): array
    {
        return [
            self::FILE_TYPE_CLASS,
            self::FILE_TYPE_INTERFACE,
            self::FILE_TYPE_TRAIT,
        ];
    }

    /**
     * @return array
     */
    protected function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return array
     */
    protected function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    protected function getImplements(): array
    {
        return $this->implements;
    }

    /**
     * @return array
     */
    protected function getTraits(): array
    {
        return $this->traits;
    }

    /**
     * @return string
     */
    protected function getExtend(): string
    {
        return '';
    }

    /**
     * @return array
     */
    protected function getUses(): array
    {
        return $this->uses;
    }

    /**
     * @return string
     */
    protected function getFileComment(): string
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getRootPath(): string
    {
        return exec('pwd') . '/src';
    }

    /**
     * @param $path
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    protected function makeDirectory($path, $mode = 0755, $recursive = true): bool
    {
        return mkdir($path, $mode, $recursive);
    }

    /**
     * @return string
     */
    protected function getNamespacePath(): string
    {
        return str_replace(
            '/',
            '\\',
            'App\\' . $this->getFolderPath()
        );
    }

    /**
     * @inheritDoc
     */
    protected function getAbsolutePath(): string
    {
        return sprintf(
            '%s/%s',
            $this->getRootPath(),
            $this->getFolderPath()
        );
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    protected function isPrimitiveType(string $type): bool
    {
        return in_array(
            $type,
            [
                'int',
                'integer',
                'string',
                'bool',
                'boolean',
                'float',
                'array',
                'callable',
            ]
        );
    }

    /**
     * Transform camelCase to under_score
     *
     * @param string $str
     * @return string
     */
    protected function transformCamelCaseToSnakeCase(string $str)
    {
        return implode(
            '_',
            array_map(function ($namePart) {
                return mb_strtolower($namePart);
            }, preg_split('/(?=[A-Z])/', $str))
        );
    }

    /**
     * @return CodeBuilder
     */
    protected function getCodeBuilder(): CodeBuilder
    {
        return new CodeBuilder();
    }

    /**
     * @return bool
     */
    private function needAddProperties(): bool
    {
        if ($this->type === self::FILE_TYPE_INTERFACE) {
            return false;
        }

        if (!$this->addProperties) {
            return false;
        }

        return true;
    }

    /**
     *
     */
    protected function initProperties(): void
    {
    }

    /**
     *
     */
    protected function initMethods(): void
    {
    }

    /**
     *
     */
    protected function initUses(): void
    {
    }

    /**
     *
     */
    protected function initTraits(): void
    {
    }

    /**
     *
     */
    protected function initImplements(): void
    {
    }
}