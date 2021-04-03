<?php

namespace Framework\Renderer;

use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRendererFactory
{

    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        $view_path = $container->get('view.path');
        $loader = new FilesystemLoader($view_path);
        $twig = new Environment($loader);
        if ($container->has('twig.extension')) {
            foreach ($container->get('twig.extension') as $extension) {
                $twig->addExtension($extension);
            }
        }
        return new TwigRenderer($twig);
    }
}
