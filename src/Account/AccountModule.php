<?php

namespace App\Account;

use App\Account\Actions\AccountAction;
use App\Account\Actions\AccountEditAction;
use App\Account\Actions\AccountHistoryAction;
use App\Account\Actions\SignupAction;
use Framework\Auth\LoggedInMiddleware;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

class AccountModule extends Module
{
    public const MIGRATIONS = __DIR__ . '/migrations';

    public const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(ContainerInterface $container)
    {
        $container->get(RendererInterface::class)->addPath('account', __DIR__ . '/templates');
        $router = $container->get(Router::class);
        $router->get($container->get('account.signup'), SignupAction::class, 'account.signup');
        $router->get($container->get('account.profile'), [LoggedInMiddleware::class, AccountAction::class], 'account.profile');
        $router->get($container->get('account.history'), [LoggedInMiddleware::class, AccountHistoryAction::class], 'account.history');
        $router->get($container->get('account.edit'), [LoggedInMiddleware::class, AccountEditAction::class], 'account.edit');
        $router->post($container->get('account.signup'), SignupAction::class);
        $router->post($container->get('account.edit'), [LoggedInMiddleware::class, AccountEditAction::class]);
    }
}
