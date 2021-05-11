<?php

namespace App\Contact;

use App\Contact\Action\ContactAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

class ContactModule extends Module
{
    public const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(ContainerInterface $container)
    {
        $container->get(RendererInterface::class)->addPath('blog', dirname(__DIR__) . '/Blog/templates');
        $router = $container->get(Router::class);
        $router->get('/contact', ContactAction::class, 'blog.contact');
        $router->post('/contact', ContactAction::class);
    }
}
