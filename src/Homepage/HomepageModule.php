<?php

namespace App\Homepage;

use App\Homepage\Actions\HomepageAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;

class HomepageModule extends Module
{

    public const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(string $prefix, Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('homepage', __DIR__ . '/templates');
        $router->get($prefix, HomepageAction::class, 'homepage.index');
        $router->get($prefix . '/accueil', HomepageAction::class, 'homepage.show');
        ;
    }
}
