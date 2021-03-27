<?php

namespace Framework\Renderer;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements RendererInterface
{

    private FilesystemLoader $loader;

    private Environment $twig;

    public function __construct(FilesystemLoader $loader, Environment $env)
    {
        $this->loader = $loader;
        $this->twig = $env;
    }

    public function addPath(string $namespace, ?string $path = null): void
    {
        try {
            $this->loader->addPath($path, $namespace);
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
}
