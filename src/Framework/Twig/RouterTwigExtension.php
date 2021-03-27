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
            new TwigFunction('path', [$this, 'pathFor'])
        ];
    }

    public function pathFor(string $path, array $params = []): string
    {
        return $this->router->setUri($path, $params);
    }
}
