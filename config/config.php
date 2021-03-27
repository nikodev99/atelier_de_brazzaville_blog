<?php

use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router;
use Framework\Router\RouterTwigExtension;

use Psr\Container\ContainerInterface;
use function DI\create;
use function DI\factory;
use function DI\get;

require dirname(__DIR__) . '/vendor/autoload.php';

return [
    'database.host' =>  '127.0.0.1',
    'database.user' =>  'root',
    'database.pass' =>  'password',
    'database.name' =>  'bzvatelier_db',
    'database.port' =>  3306,
    'view.path'  => dirname(__DIR__) . '/views',
    'twig.extension'    =>  [
        get(RouterTwigExtension::class)
    ],
    Router::class   =>  create(),
    RendererInterface::class  =>  factory(TwigRendererFactory::class),
    PDO::class  =>  function (ContainerInterface $c) {
        return new PDO(
            'mysql:dbname=' . $c->get('database.name') . ';host=' . $c->get('database.host') . ';port=' . $c->get('database.port'),
            $c->get('database.user'),
            $c->get('database.pass'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE    =>  PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE   =>  PDO::ERRMODE_EXCEPTION
            ]
        );
    }
];
