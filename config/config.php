<?php

use Framework\Mail\SwiftMailerFactory;
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router;
use Framework\Session\PHPSession;
use Framework\Session\SessionInterface;
use Framework\Twig\AdminTwigExtension;
use Framework\Twig\AuthTwigExtension;
use Framework\Twig\DateTimeTwigExtension;
use Framework\Twig\DateTwigExtension;
use Framework\Twig\FlashTwigExtension;
use Framework\Twig\FormTwigExtension;
use Framework\Twig\FrontFormTwigException;
use Framework\Twig\LayoutTwigExtension;
use Framework\Twig\PagerfantaTwigExtension;
use Framework\Twig\RouterTwigExtension;
use Framework\Twig\TextTwigExtension;
use Psr\Container\ContainerInterface;
use Twig\Extension\DebugExtension;

use function DI\create;
use function DI\factory;
use function DI\get;

require dirname(__DIR__) . '/vendor/autoload.php';

return [
    'database.host' =>  'localhost',
    'database.user' =>  'root',
    'database.pass' =>  'password',
    'database.name' =>  'bzvatelier_db',
    'database.port' =>  3306,
    'view.path'  => dirname(__DIR__) . '/views',
    'twig.extension'    =>  [
        get(DebugExtension::class),
        get(RouterTwigExtension::class),
        get(PagerfantaTwigExtension::class),
        get(TextTwigExtension::class),
        get(DateTimeTwigExtension::class),
        get(DateTwigExtension::class),
        get(FlashTwigExtension::class),
        get(FormTwigExtension::class),
        get(FrontFormTwigException::class),
        get(LayoutTwigExtension::class),
        get(AuthTwigExtension::class)
    ],
    Router::class   =>  create(),
    RendererInterface::class    =>  factory(TwigRendererFactory::class),
    SessionInterface::class     =>  create(PHPSession::class),
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
    },

    'mail.to'   =>  'latelierdebrazzaville@gmail.com',
    Swift_Mailer::class =>  factory(SwiftMailerFactory::class)
];
