<?php

namespace Akimmaksimov85\CreatorBundle\Entity;

/**
 * Class EntityDTOCreator
 * @package Akimmaksimov85\Entity
 */
class EntityNotFoundByIdExceptionCreator extends AbstractCreator
{
    const FILE_NAME_POSTFIX_NOT_FOUND_BY_ID_EXCEPTION = 'NotFoundByIdException';

    /**
     * @var string
     */
    protected $entityName;

    /**
     * EntityCreator constructor.
     *
     * @param string $folderPath
     * @param string $fileName
     * @param array $properties
     */
    public function __construct(string $folderPath, string $fileName, array $properties = [])
    {
        $this->entityName = $fileName;

        parent::__construct($folderPath, $this->makeFileName($fileName));
    }

    /**
     * @param string $entity
     *
     * @return string
     */
    protected function makeFileName(string $entity)
    {
        return $entity . self::FILE_NAME_POSTFIX_NOT_FOUND_BY_ID_EXCEPTION;
    }

    /**
     *
     */
    protected function initUses(): void
    {
        $this->uses[] = 'Symfony\\Component\\HttpFoundation\\Response';
    }

    /**
     *
     */
    protected function initMethods(): void
    {
        $this->methods = array_merge(
            $this->methods,
            $this->makeConstructor('id', $this->properties['id'] ?? 'int')
        );
    }

    /**
     * @param string $idName
     * @param string $idType
     *
     * @return array
     */
    protected function makeConstructor(string $idName, string $idType): array
    {
        return [
            '__construct' => [
                'comment' => implode(
                    "",
                    [
                        $this->getFileName() . " constructor\n\n",
                        sprintf(
                            "@param %s $%s %s ID",
                            $idType,
                            $idName,
                            $this->entityName
                        )
                    ]
                ),
                'visibility' => 'public',
                'return' => '',
                'parameters' => [$idName => $idType],
                'body' => implode(
                    "",
                    [
                        "parent::__construct(\n",
                        "    sprintf(\n",
                        "        '" . $this->entityName . " not found by id = %s',\n",
                        "        \$" . $idName . "\n",
                        "    ),\n",
                        "    Response::HTTP_NOT_FOUND\n",
                        ");\n",
                    ]
                ),
            ],
        ];
    }

    /**
     * @return string
     */
    protected function getExtend(): string
    {
        return '\\DomainException';
    }
}