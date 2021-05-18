<?php

namespace App\Account;

use App\Account\Actions\SignupAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

class AccountModule extends Module
{
    public const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(ContainerInterface $container)
    {
        $container->get(RendererInterface::class)->addPath('account', __DIR__ . '/templates');
        $router = $container->get(Router::class);
        $router->get($container->get('account.signup'), SignupAction::class, 'account.signup');
        $router->get($container->get('account.profile'), SignupAction::class, 'account.profile');
        $router->post($container->get('account.signup'), SignupAction::class);
    }
}
