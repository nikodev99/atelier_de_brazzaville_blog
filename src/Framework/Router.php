<?php

namespace Framework;

use AltoRouter;
use Exception;
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
        try {
            $this->router->map("GET", $path, $callable, $name);
        } catch (Exception $e) {
            die('Error caught ' . $e->getMessage());
        }
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

    public function setUri(string $uri, array $params = [], array $queryParams = []): ?string
    {
        $name = null;
        try {
            $name = $this->router->generate($uri, $params);
            if (!empty($queryParams)) {
                $name .= '?' . http_build_query($queryParams);
            }
        } catch (RuntimeException | Exception $r) {
            die('Error caught ' . $r->getMessage());
        }
        return $name;
    }
}
