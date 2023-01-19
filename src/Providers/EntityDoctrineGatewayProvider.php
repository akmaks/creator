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
        return $this->meta
            ->setType($this->getFileType())
            ->setFileName($this->meta->getFileName() . self::FILE_NAME_POSTFIX)
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
            $this->meta->getNamespacePath() . '\\' . $this->meta->getEntityName(
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
            ->setComment($this->meta->getFileName() . ' constructor')
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
                $this->contentBuilder
                    ->setString(
                        sprintf('Find %s by ID', $this->meta->getEntityName()),
                        "\n\n"
                    )
                    ->setString(
                        sprintf(
                            '@param %s $id %s ID',
                            $properties['id'],
                            $this->meta->getEntityName()
                        ),
                        "\n\n"
                    )
                    ->setString(sprintf('@return null|%s', $this->meta->getEntityName()))
                    ->run()
            )
            ->setProperties(['id' => $properties['id']])
            ->setVisibility(Visibility::VISIBILITY_PUBLIC->value)
            ->setBody(
                $this->contentBuilder
                    ->setString(
                        sprintf(
                            '$repository = $this->em->getRepository(%s::class);',
                            $this->meta->getEntityName()
                        )
                    )
                    ->setString(
                        sprintf(
                            '$%s = $repository->findOneBy([\'id\' => $id]);',
                            $this->meta->getEntityVarName()
                        ),
                        "\n\n"
                    )
                    ->setString(
                        sprintf(
                            'if ($%s instanceof %s) {',
                            $this->meta->getEntityVarName(),
                            $this->meta->getEntityName()
                        )
                    )
                    ->setString(
                        sprintf(
                            '    return $%s;',
                            $this->meta->getEntityVarName()
                        )
                    )
                    ->setString('}', "\n\n")
                    ->setString('return null;')
                    ->run()
            )
            ->setReturn(
                '?' . $this->meta->getEntityWithNamespace()
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
                $this->contentBuilder
                    ->setString(
                        sprintf(
                            '%s new %s',
                            ucfirst($name),
                            $this->meta->getEntityName()
                        ),
                        "\n\n"
                    )
                    ->setString(
                        sprintf(
                            '@param %s $%s %s',
                            $this->meta->getEntityName(),
                            $this->meta->getEntityVarName(),
                            $this->meta->getEntityName()
                        ),
                        "\n\n"
                    )
                    ->setString('@return void')
                    ->run()
            )
            ->setProperties(
                [
                    $this->meta->getEntityVarName() => $this->meta->getEntityName(),
                ]
            )
            ->setVisibility(Visibility::VISIBILITY_PUBLIC->value)
            ->setBody(
                $this->contentBuilder
                    ->setString(
                        sprintf(
                            '$this->em->%s($%s);',
                            $name === 'delete' ? 'remove' : 'persist',
                            $this->meta->getEntityVarName()
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