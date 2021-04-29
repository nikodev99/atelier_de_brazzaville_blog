<?php

namespace Framework\Middleware;

use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class RouterMiddleware
{
    private Router $router;

    /**
     * RouterMiddleware constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }


    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $route = $this->router->match($request);
        if (is_null($route)) {
            return $next($request);
        }
        $params = $route->getParams();
        foreach ($params as $key => $param) {
            $request = $request->withAttribute($key, $param);
        }
        $request = $request->withAttribute(get_class($route), $route);
        return $next($request);
    }
}
