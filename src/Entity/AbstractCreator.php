<?php

namespace Akimmaksimov85\Entity;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Property;
use Nette\PhpGenerator\PsrPrinter;

/**
 * Class AbstractCreator
 *
 * @package App\Infrastructure\CodeGenerators
 */
abstract class AbstractCreator
{
    /**
     * Тип файла Class
     */
    const FILE_TYPE_CLASS = 'Class';

    /**
     * Тип файла Interface
     */
    const FILE_TYPE_INTERFACE = 'Interface';

    /**
     * Тип файла Trait
     */
    const FILE_TYPE_TRAIT = 'Trait';

    /**
     * Название доменной области
     *
     * @var string
     */
    protected $domain;

    /**
     * Сущность доменной области
     *
     * @var string
     */
    protected $entity;

    /**
     * Название файла
     *
     * @var string
     */
    protected $fileName;

    /**
     * Тип файла (Class, Interface, Trait)
     *
     * @var string
     */
    protected $type;

    /**
     * AbstractCreator constructor.
     *
     * @param string $domain Название доменной области
     * @param string $entity Сущность доменной области
     * @param string $fileName Название файла
     * @param string $type Тип файла (Класс, интерфейс, трейт)
     */
    public function __construct(
        string $domain,
        string $entity,
        string $fileName,
        string $type = self::FILE_TYPE_CLASS
    ) {
        $this->domain = $domain;
        $this->entity = $entity;
        $this->fileName = $fileName;
        $this->type = ucfirst(mb_strtolower($type));

        if (in_array($this->type, $this->getFileTypes()) === false) {
            throw new \InvalidArgumentException(
                'Недопустимый тип файла %s. Допустимые типы: %s',
                $this->type,
                implode(', ', $this->getFileTypes())
            );
        }
    }

    /**
     *
     */
    public function make()
    {
        $namespace = $this->getPhpNamespace();

        foreach ($this->getUses() as $use) {
            $namespace->addUse($use);
        }

        /**
         * @var $class ClassType
         */
        $class = $namespace->{'add' . $this->type}($this->getFileName());
        $class->addComment($this->type . ' ' . $this->getFileName());

        foreach ($this->getImplements() as $implement) {
            $class->addImplement($implement);
        }

        if (empty($this->getExtend()) === false) {
            $class->addExtend($this->getExtend());
        }

        foreach ($this->getProperties() as $property => $attrs) {
            $property = $class->addProperty($property);
            $property->addComment($attrs['comment'] ?? '');
            $this->setVisibility($property, $attrs['visibility'] ?? '');
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
                $method->addParameter($parameter)->setType($type);
            }
        }

        if (file_exists($this->getFilePath()) === false) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Не существует пути %s для создания файла. Проверьте конфиги',
                    $this->getFilePath()
                )
            );
        }

        file_put_contents(
            $this->getFilePath() . '/' . $this->getFileName() . '.php',
            "<?php\n\n" . (new PsrPrinter)->printNamespace($namespace)
        );
    }

    /**
     * Метод задает область видимости свойства/метода
     *
     * @param $object Property|Method object
     * @param $visibility string
     */
    private final function setVisibility($object, string $visibility): void
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
     * Создание метода конструктора
     *
     * @param array $properties
     *
     * @return array
     */
    protected function makeConstructor(array $properties): array
    {
        $body = '';
        $comment = sprintf("%s Constructor.\n\n", $this->getFileName());

        foreach ($properties as $property => $type) {
            $comment .= sprintf("@param %s \$%s %s %s\n", $type, $property,  $this->getFileName(), $property);
            $body .= sprintf("\$this->%s = \$%s;\n", $property, $property);
        }

        return [
            '__construct' => [
                'comment' => $comment,
                'visibility' => 'public',
                'return' => '',
                'parameters' => $properties,
                'body' => $body,
            ],
        ];
    }

    /**
     * Создание метода по получению свойста
     *
     * @param string $property
     * @param string $type
     *
     * @return array
     */
    protected function makeGetPropertyMethod(string $property, string $type)
    {
        return [
            sprintf("get%s", ucfirst($property)) => [
                'comment' => sprintf(
                    "Method returns %s of %s \n\n@return %s",
                    $property,
                    $this->getFileName(),
                    $type
                ),
                'visibility' => 'public',
                'return' => $type,
                'body' => sprintf("return \$this->%s;", $property)
            ]
        ];
    }

    /**
     * Создание метода по изменению свойста
     *
     * @param string $property
     * @param string $type
     *
     * @return array
     */
    protected function makeChangePropertyMethod(string $property, string $type)
    {
        return [
            sprintf("set%s", ucfirst($property)) => [
                'comment' => sprintf(
                    "Method sets %s of %s. \n\n@param %s \$%s",
                    $property,
                    $this->getFileName(),
                    $type,
                    $property
                ),
                'parameters' => [
                    $property => $type
                ],
                'visibility' => 'public',
                'return' => 'void',
                'body' => sprintf("\$this->%s = \$%s;", $property, $property)
            ]
        ];
    }

    /**
     * Метод возвращает сущность namespace
     *
     * @return PhpNamespace
     */
    private final function getPhpNamespace(): PhpNamespace
    {
        return new PhpNamespace($this->getNamespacePath());
    }

    /**
     * Метод возвращает название доменной области
     *
     * @return string
     */
    protected function getDomain(): string
    {
        return  ucfirst(mb_strtolower($this->domain));
    }

    /**
     * Метод возвращает название сущности
     *
     * @return string
     */
    protected function getEntity(): string
    {
        return ucfirst(mb_strtolower($this->entity));
    }

    /**
     * Метод возвращает название файла
     *
     * @return string
     */
    protected function getFileName(): string
    {
        return ucfirst(mb_strtolower($this->fileName));
    }

    /**
     * Метод возвращает список доступных типов файла
     *
     * @return array
     */
    protected function getFileTypes(): array
    {
        return [
            self::FILE_TYPE_CLASS,
            self::FILE_TYPE_INTERFACE,
            self::FILE_TYPE_TRAIT
        ];
    }

    /**
     * Метод возвращает методы, которые нужно создать в файле
     *
     * @return array
     */
    protected function getMethods(): array
    {
        return [];
    }

    /**
     * Метод возвращает свойста, которые нужно создать
     *
     * @return array
     */
    protected function getProperties(): array
    {
        return [];
    }

    /**
     * Метод возвращает интерйесы, которые нужно имплементировать
     *
     * @return array
     */
    protected function getImplements(): array
    {
        return [];
    }

    /**
     * Метод возвращает класс, который нужно наследовать
     *
     * @return string
     */
    protected function getExtend(): string
    {
        return '';
    }

    /**
     * Метод возвращает список namespace, которые нужно объявить
     *
     * @return array
     */
    protected function getUses(): array
    {
        return [];
    }

    /**
     * Метод возвращает название namespace файла
     *
     * @return string
     */
    abstract protected function getNamespacePath(): string;

    /**
     * Метод возвращает путь к файлу
     *
     * @return string
     */
    abstract protected function getFilePath(): string;
}
