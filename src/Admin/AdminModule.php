<?php

namespace App\Admin;

use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRenderer;
use Framework\Router;
use Framework\Twig\AdminTwigExtension;

class AdminModule extends Module
{
    public const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(RendererInterface $renderer, Router $router, string $prefix, AdminTwigExtension $adminTwigExtension)
    {
        $renderer->addPath('admin', __DIR__ . '/templates');
        $router->get("$prefix/dashboard", DashboardAction::class, 'admin.post.index');
        if ($renderer instanceof TwigRenderer) {
            $renderer->getTwig()->addExtension($adminTwigExtension);
        }
    }
}
