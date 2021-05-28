<?php

namespace Framework\Renderer;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TwigRenderer implements RendererInterface
{

    private Environment $twig;

    public function __construct(Environment $env)
    {
        $this->twig = $env;
    }

    public function addPath(string $namespace, ?string $path = null): void
    {
        try {
            $this->twig->getLoader()->addPath($path, $namespace);
        } catch (LoaderError $e) {
            throw new \Error("The Twig renderer addPath has encountered this error: " . $e->getMessage());
        }
    }

    public function render(string $view, array $params = []): string
    {
        try {
            return $this->twig->render($view . '.twig', $params);
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
            throw new \Error("The Twig renderer render has encountered this error: " . $e->getMessage());
        }
    }

    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }

    /**
     * @return Environment
     */
    public function getTwig(): Environment
    {
        return $this->twig;
    }
}
