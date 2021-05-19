<?php

namespace Framework\Middleware;

use Exception;
use Framework\Router\Route;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DispatcherMiddleware implements MiddlewareInterface
{
    private ContainerInterface $container;

    /**
     * DispatcherMiddleware constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute(Route::class);
        if (is_null($route)) {
            return $handler->handle($request);
        }
        $callback = $route->getCallback();
        if (!is_array($callback)) {
            $callback = [$callback];
        }
        return (new CombinedMiddleware($this->container, $callback))->process($request, $handler);
    }
}
