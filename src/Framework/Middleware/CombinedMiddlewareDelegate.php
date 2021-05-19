<?php

namespace Framework\Middleware;

use Exception;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CombinedMiddlewareDelegate implements RequestHandlerInterface
{
    private array $middlewares = [];

    private int $index = 0;

    private ContainerInterface $container;
    private RequestHandlerInterface $handler;

    public function __construct(ContainerInterface $container, array $middlewares, RequestHandlerInterface $handler)
    {
        $this->container = $container;
        $this->middlewares = $middlewares;
        $this->handler = $handler;
    }

    /**
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        switch ($middleware) :
            case (is_null($middleware)):
                $this->handler->handle($request);
                break;
            case (is_callable($middleware)):
                $response = call_user_func_array($middleware, [$request, [$this, 'handle']]);
                if (is_string($response)) {
                    return new Response(200, [], $response);
                }
                return $response;
            case ($middleware instanceof MiddlewareInterface):
                return $middleware->process($request, $this);
        endswitch;
    }

    private function getMiddleware()
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            if (is_string($this->middlewares[$this->index])) {
                $middleware = $this->container->get($this->middlewares[$this->index]);
            } else {
                $middleware = $this->middlewares[$this->index];
            }
            $this->index++;
            return $middleware;
        }
        return null;
    }
}
