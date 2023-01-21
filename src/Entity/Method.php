<?php

declare(strict_types=1);

namespace Akimmaksimov85\CreatorBundle\Entity;

class Method
{
    private string $name = '';
    private string $comment = '';
    private array $parameters = [];
    private string $visibility = '';
    private string $body = '';
    private string $return = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getProperties(): array
    {
        return $this->parameters;
    }

    public function setProperties(array $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getReturn(): string
    {
        return $this->return;
    }

    public function setReturn(string $return): static
    {
        $this->return = $return;

        return $this;
    }

    public function setVoidReturn(): static
    {
        $this->return = 'void';

        return $this;
    }

    public function setStaticReturn(): static
    {
        $this->return = 'static';

        return $this;
    }
}