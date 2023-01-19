<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\Builders\PhpFileBuilder;

use Akimmaksimov85\CreatorBundle\Entity\Method;
use Akimmaksimov85\CreatorBundle\Entity\Meta;
use Akimmaksimov85\CreatorBundle\Entity\Property;
use Akimmaksimov85\CreatorBundle\Enums\Visibility;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;

class PhpFileBuilder
{
    /**
     * @param Meta $meta
     * @return void
     */
    public function run(Meta $meta): void
    {
        $file = $this->getFile();
        $file->setStrictTypes();

        $namespace = $file->addNamespace($meta->getNamespacePath());

        foreach ($meta->getUses() as $use) {
            $namespace->addUse($use);
        }

        $class = $namespace->{'add' . $meta->getType()}($meta->getFileName());
        $class->addComment($meta->getClassComment());

        foreach ($meta->getImplements() as $implement) {
            $class->addImplement($implement);
        }

        if (empty($meta->getExtends()) === false) {
            $class->setExtends($meta->getExtends());
            $class->addComment($meta->getFileComment());
        }

        foreach ($meta->getTraits() as $trait) {
            $class->addTrait($trait);
        }

        if ($meta->needAddProperties()) {
            /**
             * @var Property $providerProperty
             */
            foreach ($meta->getProperties() as $providerProperty) {
                $property = $class->addProperty($providerProperty->getName());
                $property->addComment($providerProperty->getComment());
                $property->setType($providerProperty->getType());
                $this->setVisibility($property, $providerProperty->getVisibility());
            }
        }

        /**
         * @var Method $providerMethod
         */
        foreach ($meta->getMethods() as $providerMethod) {
            $method = $class->addMethod($providerMethod->getName());

            $method->addComment($providerMethod->getComment());
            $this->setVisibility($method, $providerMethod->getVisibility());
            $method->setReturnType($providerMethod->getReturn());

            if (!$meta->isTypeInterface()) {
                $method->setBody($providerMethod->getBody());
            }

            foreach ($providerMethod->getProperties() as $parameter => $type) {
                if ($method->getName() !== '__construct') {
                    $method->addParameter($parameter)->setType($type);
                } else {
                    $method->addPromotedParameter($parameter)
                           ->setReadOnly()
                           ->setVisibility(Visibility::VISIBILITY_PRIVATE->value)
                           ->setType($type);
                }
            }
        }

        if (is_dir($this->getAbsolutePath($meta->getFolderPath())) === false) {
            $this->makeDirectory($this->getAbsolutePath($meta->getFolderPath()));
        }

        file_put_contents(
            $this->generateFileName($meta->getFolderPath(), $meta->getFileName()),
            $this->clearBackSlashes($this->getPsrPrinter()->printFile($file))
        );
    }

    /**
     * @param string $folderPath
     * @param string $fileName
     * @return string
     */
    private function generateFileName(string $folderPath, string $fileName): string
    {
        return $this->getAbsolutePath($folderPath) . '/' . $fileName . '.php';
    }

    /**
     * @param string $file
     * @return string
     */
    private function clearBackSlashes(string $file): string
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
     * @param mixed $object
     * @param string $visibility
     * @return void
     */
    private function setVisibility(mixed $object, string $visibility): void
    {
        if (empty($visibility) === true) {
            return;
        }

        $setVisibilityMethod = 'set' . ucfirst($visibility);

        if (method_exists($object, $setVisibilityMethod) === false) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Undefined visibility type %s. Check config',
                    $visibility
                )
            );
        }

        $object->$setVisibilityMethod();
    }

    /**
     * @param string $path
     * @param int $mode
     * @param bool $recursive
     * @return void
     */
    private function makeDirectory(string $path, int $mode = 0755, bool $recursive = true): void
    {
        mkdir($path, $mode, $recursive);
    }

    /**
     * @param string $folderPath
     * @return string
     */
    private function getAbsolutePath(string $folderPath): string
    {
        return sprintf(
            '%s/%s',
            exec('pwd') . '/src',
            $folderPath
        );
    }

    /**
     * @return \Nette\PhpGenerator\PsrPrinter
     */
    private function getPsrPrinter(): PsrPrinter
    {
        return new PsrPrinter();
    }

    /**
     * @return \Nette\PhpGenerator\PhpFile
     */
    private function getFile(): PhpFile
    {
        return new PhpFile();
    }
}