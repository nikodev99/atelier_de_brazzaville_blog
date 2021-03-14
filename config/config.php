<?php

use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router;
use Framework\Router\RouterTwigExtension;

use function DI\create;
use function DI\factory;
use function DI\get;

require '../vendor/autoload.php';

return [
    'view.path'  => dirname(__DIR__) . '/views',
    'twig.extension'    =>  [
        get(RouterTwigExtension::class)
    ],
    Router::class   =>  create(),
    RendererInterface::class  =>  factory(TwigRendererFactory::class)
];