<?php

use App\Blog\Table\PostTable;
use App\Shop\Actions\ProductShowAction;
use App\Shop\ShopWidget;
use App\Shop\Table\ProductsTable;
use App\Shop\Table\PurchaseTable;
use Framework\Api\Stripe\StripePurchase;
use Framework\Auth;
use Framework\Renderer\RendererInterface;

use function DI\add;
use function DI\get;
use function DI\create;

return [
    "admin.widgets"    =>  add([
      get(ShopWidget::class)
    ]),
    ProductShowAction::class => create()->constructor(
        get(RendererInterface::class),
        get(PostTable::class),
        get(ProductsTable::class),
        get(PurchaseTable::class),
        get(Auth::class),
        get("stripe.publicKey")
    ),
    StripePurchase::class => create()->constructor(get("stripe.secretKey"))
];
