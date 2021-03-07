<?php

namespace Framework;

use Exception;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App
{

    private array $modules = [];

    private Router $router;

    public function __construct(array $modules = [])
    {
        $this->router = new Router();
        foreach ($modules as $module) {
            $this->modules[] = new $module($this->router);
        }
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        if (!empty($uri) && $uri[-1] === "/") {
            return (new Response(301))->withHeader('Location', substr($uri, 0, -1));
        }
        $route = $this->router->match($request);
        if (is_null($route)) {
            return new Response(404, [], '<h1>Error 404 page not found</h1>');
        }
        $params = $route->getParams();
        foreach ($params as $key => $param) {
            $request = $request->withAttribute($key, $param);
        }
        $response = call_user_func_array($route->getCallback(), [$request]);
        switch ($response) {
            case is_string($response):
                return new Response(200, [], $response);
                break;
            case $response instanceof ResponseInterface:
                return $response;
                break;
            default:
                throw new Exception('The response is neither a string nor an instance of Response Interface');
                break;
        }
    }
}
