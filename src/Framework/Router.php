<?php

namespace Framework;

use AltoRouter;
use App\Blog\Actions\AdminBlogAction;
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

    public function get(string $path, $callable, ?string $name = null): self
    {
        try {
            $this->router->map("GET", $path, $callable, $name);
        } catch (Exception $e) {
            die('Error caught ' . $e->getMessage());
        }
        return $this;
    }

    public function post(string $path, $callable, ?string $name = null): self
    {
        try {
            $this->router->map("POST", $path, $callable, $name);
        } catch (Exception $e) {
            die('Error caught ' . $e->getMessage());
        }
        return $this;
    }

    public function delete(string $path, $callable, ?string $name = null): self
    {
        try {
            $this->router->map("DELETE", $path, $callable, $name);
        } catch (Exception $e) {
            die('Error caught ' . $e->getMessage());
        }
        return $this;
    }

    public function match(ServerRequestInterface $request): ?Route
    {
        $uri = $request->getUri()->getPath();
        $result = $this->router->match($uri);
        if (is_array($result)) {
            return new Route(
                $result['target'],
                $result['params'],
                $result['name']
            );
        } else {
            if ($request->getMethod() === 'DELETE') {
                $id = (int) substr($uri, mb_strrpos($uri, '/') + 1);
                if (is_int($id)) {
                    return new Route(
                        AdminBlogAction::class,
                        ['id' => $id],
                        'admin.post.delete'
                    );
                }
            }
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
