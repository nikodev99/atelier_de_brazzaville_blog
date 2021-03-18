<?php

namespace Framework;

use AltoRouter;
use Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class Router
{

    private AltoRouter $router;

    public function __construct()
    {
        $this->router = new AltoRouter();
    }

    public function get(string $path, $callable, string $name): self
    {
        $this->router->map("GET", $path, $callable, $name);
        return $this;
    }

    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->router->match($request->getUri()->getPath());
        if (is_array($result)) {
            return new Route(
                $result['name'],
                $result['target'],
                $result['params']
            );
        }
        return null;
    }

    public function setUri(string $uri, array $params = []): ?string
    {
        $name = null;
        try {
            $name = $this->router->generate($uri, $params);
        } catch (RuntimeException $r) {
            die('Error caught ' + $r->getMessage());
        }
        return $name;
    }
}
