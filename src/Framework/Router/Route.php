<?php

namespace Framework\Router;

class Route
{

    private ?string $name;

    /** @var string|callable */
    private $callable;

    private array $params;

    public function __construct($callable, array $params, ?string $name = null)
    {
        $this->name = $name;
        $this->callable = $callable;
        $this->params = $params;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCallback()
    {
        return $this->callable;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
