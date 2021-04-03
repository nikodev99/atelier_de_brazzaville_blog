<?php

namespace App\Blog;

use App\Blog\Actions\AdminBlogAction;
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
        $router
            ->get($container->get('blog.prefix'), BlogAction::class, 'blog.index')
            ->get($container->get('blog.prefix') . '/[*:slug]-[i:id]', BlogAction::class, 'blog.show')
        ;

        if ($container->has('admin.prefix')) {
            $adminPrefix = $container->get('admin.prefix');
            $router
                ->get("$adminPrefix/dashboard", AdminBlogAction::class, 'admin.posts.index')
                ->get("$adminPrefix/posts", AdminBlogAction::class, 'blog.admin.posts')
                ->get("$adminPrefix/post/new", AdminBlogAction::class, 'admin.post.create')
                ->get("$adminPrefix/post/[i:id]", AdminBlogAction::class, 'admin.post.edit')
                ->post("$adminPrefix/post/[i:id]", AdminBlogAction::class)
                ->post("$adminPrefix/post/new", AdminBlogAction::class)
                ->delete("$adminPrefix/delete/[i:id]", AdminBlogAction::class, 'admin.post.delete')
            ;
        }
    }
}
