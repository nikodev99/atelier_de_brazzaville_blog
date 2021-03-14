<?php

namespace Framework\Router;

class Route
{

    private string $name;

    /** @var string|callable */
    private $callable;

    private array $params;

    public function __construct(string $name, $callable, array $params)
    {
        $this->name = $name;
        $this->callable = $callable;
        $this->params = $params;
    }

    public function getName(): string
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
