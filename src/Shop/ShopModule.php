<?php

namespace App\Shop;

use App\Shop\Actions\AdminProductAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

class ShopModule extends Module
{
    public const MIGRATIONS = __DIR__ . '/db/migrations';

    public const SEEDS = __DIR__ . '/db/seeds';

    public const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(ContainerInterface $container)
    {
        $container->get(RendererInterface::class)->addPath('shop', __DIR__ . '/templates');
        $router = $container->get(Router::class);
        $shopPrefix = $container->get('admin.prefix');
        $router->crud("$shopPrefix/products", AdminProductAction::class, 'admin.shop.product');
    }
}
