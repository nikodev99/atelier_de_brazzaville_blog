<?php

namespace Framework;

use DI\ContainerBuilder;
use Exception;
use Framework\Middleware\RoutePrefixedMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class App implements RequestHandlerInterface
{

    private array $modules = [];

    /**
     * @var string|array|null
     */
    private $definition;

    /**
     * @var ContainerInterface
     */
    private $container;

    private array $middlewares = [];

    private int $index = 0;

    public function __construct($definition = null)
    {
        $this->definition = $definition;
    }

    public function addModule(string $module): self
    {
        $this->modules[] = $module;
        return $this;
    }

    /**
     * @param string|callable|MiddlewareInterface $middleware
     *
     * @throws Exception
     */
    public function pipe($middleware, ?string $router_prefix = null): self
    {
        if (!is_null($router_prefix)) {
            $this->middlewares[] = new RoutePrefixedMiddleware($this->getContainer(), $router_prefix, $middleware);
        } else {
            $this->middlewares[] = $middleware;
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (is_null($middleware)) {
            throw new Exception("Request handled by none middleware");
        } elseif (is_callable($middleware)) {
            return call_user_func_array($middleware, [$request, [$this, 'handle']]);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
    }

    /**
     * @throws Exception
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }
        return $this->handle($request);
    }

    /**
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * @throws Exception
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();
            if ($this->definition) {
                $builder->addDefinitions($this->definition);
            }
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
    private function getMiddleware()
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            if (is_string($this->middlewares[$this->index])) {
                $middleware = $this->getContainer()->get($this->middlewares[$this->index]);
            } else {
                $middleware = $this->middlewares[$this->index];
            }
            $this->index++;
            return $middleware;
        }
        return null;
    }
}
