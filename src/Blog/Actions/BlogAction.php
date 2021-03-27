<?php

namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface;

class BlogAction
{
    use RouterAwareAction;

    private RendererInterface $renderer;

    /**
     * @var Router
     */
    private Router $router;

    /**
     * @var PostTable
     */
    private PostTable $postTable;

    public function __construct(RendererInterface $renderer, Router $router, PostTable $postTable)
    {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postTable = $postTable;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        if ($request->getAttribute('id')) {
            return $this->show($request);
        }
        return $this->index($request);
    }

    public function index(ServerRequestInterface $request): string
    {
        $params = $request->getQueryParams();
        $currentPage = $params['p'] ?? 1;
        $posts = $this->postTable->findPaginated(6, $currentPage);
        return $this->renderer->render('@blog/index', compact('posts'));
    }

    public function show(ServerRequestInterface $request)
    {
        $slug = $request->getAttribute('slug');
        $post = $this->postTable->find((int) $request->getAttribute('id'));
        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'slug'  =>  $post->slug,
                'id'    =>  $post->id
            ]);
        }
        return $this->renderer->render('@blog/show', [
            'post'  =>  $post
        ]);
    }
}
