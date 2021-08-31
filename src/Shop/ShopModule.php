<?php

namespace App\Shop;

use App\Shop\Actions\AdminProductAction;
use App\Shop\Actions\CheckoutAction;
use App\Shop\Actions\InvoiceAction;
use App\Shop\Actions\ProductListingAction;
use App\Shop\Actions\ProductShowAction;
use App\Shop\Actions\PurchaseProcessAction;
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
        $router->get("/boutique-ephemere/invoice-[i:id]", [LoggedInMiddleware::class, InvoiceAction::class], "shop.invoice");
        $router->post("/boutique-ephemere/product[i:id]-process", [LoggedInMiddleware::class, PurchaseProcessAction::class], "shop.charge");
        $router->get("/boutique-ephemere/[*:slug]", ProductShowAction::class, "shop.show");
        $router->get("/checkout-success", [LoggedInMiddleware::class, CheckoutAction::class], "shop.payment.success");
        $router->get("/checkout-cancel", [LoggedInMiddleware::class, CheckoutAction::class], "shop.payment.cancel");
        $router->crud("$shopPrefix/products", AdminProductAction::class, 'admin.shop.product');
    }
}
