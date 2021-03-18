<?php

namespace App\Homepage;

use Framework\Module;
use Framework\Router;
use App\Homepage\Actions\HomepageAction;
use Framework\Renderer\RendererInterface;

class HomepageModule extends Module
{

    public const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(string $prefix, Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('homepage', __DIR__ . '/templates');
        $router
            ->get($prefix, HomepageAction::class, 'homepage.index')
            ->get($prefix . '/acceuil', HomepageAction::class, 'homepage.show')
        ;
    }
}
