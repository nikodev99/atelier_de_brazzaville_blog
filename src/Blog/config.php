<?php

use App\Blog\BlogModule;
use Framework\Renderer\RendererInterface;
use Framework\Router;

use function DI\create;
use function DI\get;

return [
    'blog.prefix'   =>  '/blog',
    BlogModule::class   =>  create()->constructor(get('blog.prefix'), get(Router::class), get(RendererInterface::class))
];
