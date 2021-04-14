<?php

namespace App\Blog;

use App\Blog\Actions\CategoryCrudAction;
use App\Blog\Actions\PostCrudAction;
use App\Blog\Actions\BlogAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

class BlogModule extends Module
{

    public const DEFINITIONS = __DIR__ . '/config.php';

    public const MIGRATIONS = __DIR__ . '/db/migrations';

    public const SEEDS = __DIR__ . '/db/seeds';

    public function __construct(ContainerInterface $container)
    {
        $container->get(RendererInterface::class)->addPath('blog', __DIR__ . '/templates');
        $router = $container->get(Router::class);
        $router->get($container->get('blog.prefix'), BlogAction::class, 'blog.index');
        $router->get($container->get('blog.prefix') . '/[*:slug]-[i:id]', BlogAction::class, 'blog.show');

        if ($container->has('admin.prefix')) {
            $adminPrefix = $container->get('admin.prefix');
            $router->get("$adminPrefix/dashboard", PostCrudAction::class, 'admin.post.index');
            $router->crud("$adminPrefix/posts", PostCrudAction::class, "admin.post");
            $router->crud("$adminPrefix/categories", CategoryCrudAction::class, "admin.post.category");
        }
    }
}
