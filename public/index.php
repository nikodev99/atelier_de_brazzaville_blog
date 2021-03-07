<?php

use Whoops\Run;
use Framework\App;
use App\Blog\BlogModule;
use Framework\Renderer;
use GuzzleHttp\Psr7\ServerRequest;
use Whoops\Handler\PrettyPageHandler;

require '../vendor/autoload.php';

$whoops = new Run();
$whoops->pushHandler(new PrettyPageHandler());
$whoops->register();

$renderer = new Renderer();
$renderer->addPath(dirname(__DIR__) . '/views');

$app = new App([
    BlogModule::class
], [
    'renderer'  =>  $renderer
]);

$response = $app->run(ServerRequest::fromGlobals());
Http\Response\send($response);
