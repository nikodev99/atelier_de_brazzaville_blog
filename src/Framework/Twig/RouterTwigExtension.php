<?php

namespace Framework\Twig;

use Framework\Router;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RouterTwigExtension extends AbstractExtension
{

    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('path', [$this, 'pathFor']),
            new TwigFunction('is_path', [$this, 'subPath'])
        ];
    }

    public function pathFor(string $path, array $params = []): string
    {
        return $this->router->setUri($path, $params);
    }

    public function subPath(string $path): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $expectedUri = $this->router->setUri($path);
        if (strpos($uri, $expectedUri) !== false) {
            return 'active';
        }
        return '';
    }
}
