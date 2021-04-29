<?php

use App\Admin\AdminModule;
use Framework\App;
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

$modules = [
    HomepageModule::class,
    BlogModule::class,
    AdminModule::class
];

$app = (new App(dirname(__DIR__) . '/config/config.php'))
    ->addModule(HomepageModule::class)
    ->addModule(BlogModule::class)
    ->addModule(AdminModule::class)
    ->pipe(TrailingSlashMiddleware::class)
    ->pipe(MethodMiddleware::class)
    ->pipe(RouterMiddleware::class)
    ->pipe(DispatcherMiddleware::class)
    ->pipe(NotFoundMiddleware::class)
;

if (php_sapi_name() !== 'cli') {
    $response = $app->run(ServerRequest::fromGlobals());
    Http\Response\send($response);
}
