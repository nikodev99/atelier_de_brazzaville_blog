<?php

use App\Account\AccountModule;
use App\Admin\AdminModule;
use App\Auth\AuthModule;
use App\Auth\ForbiddenMiddleware;
use App\Auth\UserNotFoundMiddleware;
use App\Contact\ContactModule;
use App\Shop\ShopModule;
use Framework\App;
use Framework\Auth\RoleMiddlewareFactory;
use Framework\Middleware\DispatcherMiddleware;
use Framework\Middleware\MethodMiddleware;
use Framework\Middleware\NotFoundMiddleware;
use Framework\Middleware\RouterMiddleware;
use Framework\Middleware\TrailingSlashMiddleware;
use Whoops\Run;
use App\Blog\BlogModule;
use App\Homepage\HomepageModule;
use GuzzleHttp\Psr7\ServerRequest;
use Whoops\Handler\PrettyPageHandler;

require dirname(__DIR__) . '/vendor/autoload.php';

$whoops = new Run();
$whoops->pushHandler(new PrettyPageHandler());
$whoops->register();

$app = (new App([dirname(__DIR__) . '/config/config.php', dirname(__DIR__) . '/config.php']))
    ->addModule(HomepageModule::class)
    ->addModule(BlogModule::class)
    ->addModule(ContactModule::class)
    ->addModule(AdminModule::class)
    ->addModule(AuthModule::class)
    ->addModule(AccountModule::class)
    ->addModule(ShopModule::class)
;

$container = $app->getContainer();
$app->pipe(TrailingSlashMiddleware::class)
    ->pipe(UserNotFoundMiddleware::class)
    ->pipe(ForbiddenMiddleware::class)
    ->pipe($container->get(RoleMiddlewareFactory::class)->makeForRole('admin'), $container->get("admin.prefix"))
    ->pipe(MethodMiddleware::class)
    ->pipe(RouterMiddleware::class)
    ->pipe(DispatcherMiddleware::class)
    ->pipe(NotFoundMiddleware::class)
;

if (php_sapi_name() !== 'cli') {
    $response = $app->run(ServerRequest::fromGlobals());
    Http\Response\send($response);
}
