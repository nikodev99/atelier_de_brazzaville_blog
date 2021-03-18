<?php

use App\Homepage\HomepageModule;
use Framework\Renderer\RendererInterface;
use Framework\Router;

use function DI\create;
use function DI\get;

return [
    'homepage.prefix'   =>  '',
    HomepageModule::class   =>  create()->constructor(
        get('homepage.prefix'),
        get(Router::class),
        get(RendererInterface::class)
    )
];
