<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\Providers;

use Akimmaksimov85\CreatorBundle\Entity\Method;
use Akimmaksimov85\CreatorBundle\Entity\Meta;
use Akimmaksimov85\CreatorBundle\Enums\FileTypes;
use Akimmaksimov85\CreatorBundle\Enums\Visibility;

class EntityDoctrineGatewayProvider extends AbstractDataProvider
{
    public const FILE_NAME_POSTFIX = 'Gateway';

    /**
     * @param array $properties
     * @return Meta
     */
    public function getFilledGenerator(array $properties): Meta
    {
        return $this->getMeta()
            ->setType($this->getFileType())
            ->setFileName($this->getMeta()->getFileName() . self::FILE_NAME_POSTFIX)
            ->setUses($this->getUses())
            ->setImplements($this->getImplements())
            ->setMethods($this->getMethods($properties));
    }

    /**
     * @return string[]
     */
    private function getUses(): array
    {
        return [
            'Doctrine\\ORM\\EntityManagerInterface',
        ];
    }

    /**
     * @return string[]
     */
    private function getImplements(): array
    {
        return [
            $this->getMeta()->getNamespacePath() . '\\' . $this->getMeta()->getEntityName(
            ) . EntityGatewayInterfaceProvider::FILE_NAME_POSTFIX,
        ];
    }

    /**
     * @param array $properties
     * @return \Akimmaksimov85\CreatorBundle\Entity\Method[]
     */
    private function getMethods(array $properties): array
    {
        return [
            $this->makeConstruct(),
            $this->makeGetByIdMethod($properties),
            $this->makeCrudMethod('create'),
            $this->makeCrudMethod('update'),
            $this->makeCrudMethod('delete'),
        ];
    }

    /**
     * @return \Akimmaksimov85\CreatorBundle\Entity\Method
     */
    private function makeConstruct(): Method
    {
        return (new Method())
            ->setName('__construct')
            ->setComment($this->getMeta()->getFileName() . ' constructor')
            ->setVisibility(Visibility::VISIBILITY_PUBLIC->value)
            ->setProperties(['em' => 'Doctrine\\ORM\\EntityManagerInterface']);
    }

    /**
     * @param array $properties
     * @return Method
     */
    private function makeGetByIdMethod(array $properties): Method
    {
        return (new Method())
            ->setName('getById')
            ->setComment(
                $this->getContentBuilder()
                    ->setString(
                        sprintf('Find %s by ID', $this->getMeta()->getEntityName()),
                        "\n\n"
                    )
                    ->setString(
                        sprintf(
                            '@param %s $id %s ID',
                            $properties['id'],
                            $this->getMeta()->getEntityName()
                        ),
                        "\n\n"
                    )
                    ->setString(sprintf('@return null|%s', $this->getMeta()->getEntityName()))
                    ->run()
            )
            ->setProperties(['id' => $properties['id']])
            ->setVisibility(Visibility::VISIBILITY_PUBLIC->value)
            ->setBody(
                $this->getContentBuilder()
                    ->setString(
                        sprintf(
                            '$repository = $this->em->getRepository(%s::class);',
                            $this->getMeta()->getEntityName()
                        )
                    )
                    ->setString(
                        sprintf(
                            '$%s = $repository->findOneBy([\'id\' => $id]);',
                            $this->getMeta()->getEntityVarName()
                        ),
                        "\n\n"
                    )
                    ->setString(
                        sprintf(
                            'if ($%s instanceof %s) {',
                            $this->getMeta()->getEntityVarName(),
                            $this->getMeta()->getEntityName()
                        )
                    )
                    ->setString(
                        sprintf(
                            '    return $%s;',
                            $this->getMeta()->getEntityVarName()
                        )
                    )
                    ->setString('}', "\n\n")
                    ->setString('return null;')
                    ->run()
            )
            ->setReturn(
                '?' . $this->getMeta()->getEntityWithNamespace()
            );
    }

    /**
     * @param string $name
     * @return Method
     */
    private function makeCrudMethod(string $name): Method
    {
        return (new Method())
            ->setName($name)
            ->setComment(
                $this->getContentBuilder()
                    ->setString(
                        sprintf(
                            '%s new %s',
                            ucfirst($name),
                            $this->getMeta()->getEntityName()
                        ),
                        "\n\n"
                    )
                    ->setString(
                        sprintf(
                            '@param %s $%s %s',
                            $this->getMeta()->getEntityName(),
                            $this->getMeta()->getEntityVarName(),
                            $this->getMeta()->getEntityName()
                        ),
                        "\n\n"
                    )
                    ->setString('@return void')
                    ->run()
            )
            ->setProperties(
                [
                    $this->getMeta()->getEntityVarName() => $this->getMeta()->getEntityName(),
                ]
            )
            ->setVisibility(Visibility::VISIBILITY_PUBLIC->value)
            ->setBody(
                $this->getContentBuilder()
                    ->setString(
                        sprintf(
                            '$this->em->%s($%s);',
                            $name === 'delete' ? 'remove' : 'persist',
                            $this->getMeta()->getEntityVarName()
                        )
                    )
                    ->setString('$this->em->flush();')
                    ->run()

            )
            ->setVoidReturn();
    }

    /**
     * @return string
     */
    private function getFileType(): string
    {
        return FileTypes::FILE_TYPE_CLASS->value;
    }
}