<?php

namespace Framework;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App
{

    private array $modules = [];

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container, array $modules = [])
    {
        $this->container = $container;
        foreach ($modules as $module) {
            $this->modules[] = $this->container->get($module);
        }
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        $parseBody = $request->getParsedBody();
        if (array_key_exists('_METHOD', $parseBody) && in_array($parseBody['_METHOD'], ['DELETE', 'PUT'])) {
            $request = $request->withMethod($parseBody['_METHOD']);
        }
        if (!empty($uri) && $uri[-1] === "/" && strlen($uri) > 1) {
            return (new Response(301))->withHeader('Location', substr($uri, 0, -1));
        }
        $router = $this->container->get(Router::class);
        $route = $router->match($request);
        if (is_null($route)) {
            return new Response(404, [], '<h1>Error 404 page not found</h1>');
        }
        $params = $route->getParams();
        foreach ($params as $key => $param) {
            $request = $request->withAttribute($key, $param);
        }
        $callback = $route->getCallback();
        if (is_string($callback)) {
            $callback = $this->container->get($callback);
        }
        $response = call_user_func_array($callback, [$request]);
        switch ($response) {
            case is_string($response):
                return new Response(200, [], $response);
            case $response instanceof ResponseInterface:
                return $response;
            default:
                die('The response is neither a string nor an instance of Response Interface');
        }
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
