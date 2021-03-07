<?php

namespace Framework\Router;

class Route
{

    private string $name;

    /** @var callable */
    private $callable;

    private array $params;

    public function __construct(string $name, callable $callable, array $params)
    {
        $this->name = $name;
        $this->callable = $callable;
        $this->params = $params;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCallback(): callable
    {
        return $this->callable;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
