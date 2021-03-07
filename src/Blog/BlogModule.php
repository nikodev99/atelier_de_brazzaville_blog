<?php

namespace App\Blog;

use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BlogModule
{

    public function __construct(Router $router)
    {
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/[*:slug]', [$this, 'show'], 'blog.show');
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        return new Response(200, [], '<h1>Welcome on the atelier of brazzaville blog</h1>');
    }

    public function show(ServerRequestInterface $request): ResponseInterface
    {
        return new Response(200, [], '<h1>Bienvenu sur ' . $request->getAttribute('slug') . '</h1>');
    }
}
