<?php

namespace Framework;

use DI\ContainerBuilder;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App
{

    private array $modules = [];

    private string $definition;

    /**
     * @var ContainerInterface
     */
    private $container;

    private array $middlewares;

    private int $index = 0;

    public function __construct(string $definition)
    {
        $this->definition = $definition;
    }

    public function addModule(string $module): self
    {
        $this->modules[] = $module;
        return $this;
    }

    public function pipe(string $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function process(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (is_null($middleware)) {
            throw new Exception("Request handled by none middleware");
        }
        return call_user_func_array($middleware, [$request, [$this, 'process']]);
    }

    /**
     * @throws Exception
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }
        return $this->process($request);
    }

    /**
     * @throws Exception
     */
    private function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();
            $builder->addDefinitions($this->definition);
            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }
            $this->container = $builder->build();
        }
        return $this->container;
    }

    /**
     * @throws Exception
     */
    private function getMiddleware(): ?callable
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            $middleware = $this->getContainer()->get($this->middlewares[$this->index]);
            $this->index++;
            return $middleware;
        }
        return null;
    }
}
