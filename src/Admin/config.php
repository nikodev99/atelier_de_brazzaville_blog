<?php

use App\Admin\Actions\DashboardAction;
use App\Admin\AdminModule;
use App\Admin\Tables\MessageTable;
use App\Auth\Table\UserTable;
use App\Blog\Table\CommentTable;
use App\Blog\Table\PostTable;
use App\Shop\Table\ProductsTable;
use App\Shop\Table\PurchaseTable;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Twig\AdminTwigExtension;

use function DI\add;
use function DI\create;
use function DI\get;

return [
    'admin.prefix'  =>  '/admin',
    'admin.widgets' =>  add([]),
    AdminTwigExtension::class   => create()->constructor(get('admin.widgets')),
    AdminModule::class   =>  create()->constructor(
        get(RendererInterface::class),
        get(Router::class),
        get('admin.prefix'),
        get(AdminTwigExtension::class)
    ),
    DashboardAction::class   =>  create()->constructor(
        get(RendererInterface::class),
        get(PostTable::class),
        get(UserTable::class),
        get(ProductsTable::class),
        get(PurchaseTable::class),
        get(CommentTable::class),
        get(MessageTable::class),
        get('admin.widgets')
    )
];
