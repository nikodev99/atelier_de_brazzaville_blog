<?php

namespace App\Blog\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class BlogAction
{


    private RendererInterface $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $slug = $request->getAttribute('slug');
        if ($slug) {
            return $this->show($slug);
        }
        return $this->index();
    }

    public function index(): string
    {
        return $this->renderer->render('@blog/index');
    }

    public function show($slug): string
    {
        return $this->renderer->render('@blog/show', [
            'slug'  => $slug
        ]);
    }
}
