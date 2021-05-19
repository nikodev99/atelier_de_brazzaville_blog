<?php

namespace Framework\Middleware;

use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CombinedMiddleware implements MiddlewareInterface
{
    private ContainerInterface $container;
    private array $middlewares;

    public function __construct(ContainerInterface $container, array $middlewares)
    {
        $this->container = $container;
        $this->middlewares = $middlewares;
    }

    /**
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $handler = new CombinedMiddlewareDelegate($this->container, $this->middlewares, $handler);
        return $handler->handle($request);
    }
}
