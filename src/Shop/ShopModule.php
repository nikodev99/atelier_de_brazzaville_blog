<?php

namespace App\Shop;

use App\Shop\Actions\AdminProductAction;
use App\Shop\Actions\ProductListingAction;
use App\Shop\Actions\ProductShowAction;
use App\Shop\Actions\PurchaseProcessAction;
use App\Shop\Actions\PurchaseRecapAction;
use Framework\Auth\LoggedInMiddleware;
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
        $router->get("/boutique-ephemere", ProductListingAction::class, "shopping");
        $router->post("/boutique-ephemere/product[i:id]-recap", [LoggedInMiddleware::class, PurchaseRecapAction::class], "shop.recap");
        $router->post("/boutique-ephemere/product[i:id]-process", [LoggedInMiddleware::class, PurchaseProcessAction::class], "shop.charge");
        $router->get("/boutique-ephemere/[*:slug]", ProductShowAction::class, "shop.show");
        $router->crud("$shopPrefix/products", AdminProductAction::class, 'admin.shop.product');
    }
}
