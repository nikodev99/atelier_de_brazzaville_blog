<?php

namespace App\Blog;

use Framework\Renderer;
use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BlogModule
{

    private Renderer $renderer;

    public function __construct(Router $router, Renderer $renderer)
    {
        $this->renderer = $renderer;
        $this->renderer->addPath('blog', __DIR__ . '/templates');
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/[*:slug]', [$this, 'show'], 'blog.show');
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        return new Response(200, [], $this->renderer->render('@blog/index'));
    }

    public function show(ServerRequestInterface $request): ResponseInterface
    {
        return new Response(200, [], $this->renderer->render('@blog/show', [
            'slug'  => $request->getAttribute('slug')
        ]));
    }
}
