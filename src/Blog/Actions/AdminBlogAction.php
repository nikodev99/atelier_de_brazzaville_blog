<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use DateTime;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AdminBlogAction
{
    use RouterAwareAction;

    private RendererInterface $renderer;

    private Router $router;

    private PostTable $postTable;

    private FlashService $flash;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $postTable,
        FlashService $flash
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postTable = $postTable;
        $this->flash = $flash;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr($request->getUri()->getPath(), -5) === 'posts') {
            return $this->posts();
        }
        if (substr($request->getUri()->getPath(), -3) === 'new') {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        return $this->index($request);
    }

    public function index(ServerRequestInterface $request): string
    {
        $params = $request->getQueryParams();
        $currentPage = $params['p'] ?? 1;
        $items = $this->postTable->findPaginated(6, $currentPage);
        return $this->renderer->render('@blog/admin/dashboard', compact('items'));
    }

    public function posts(): string
    {
        $items = $this->postTable->findAll();
        return $this->renderer->render('@blog/admin/posts', compact('items'));
    }

    public function create(ServerRequestInterface $request): string
    {
        $errors = [];
        $item = null;
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $id = $this->postTable->add($params);
                $item = $this->postTable->find($id);
                $this->flash->success('L\'article à bien été ajouté');
                return $this->renderer->render('@blog/admin/create', compact('item'));
            }
            $errors = $validator->getErrors();
            $item = $params;
            $this->flash->error('Le système d\'ajout d\'article à rencontrée une ou plusieurs erreurs');
        }
        $item = new Post();
        $item->created_date = new DateTime();
        return $this->renderer->render('@blog/admin/create', compact('item', 'errors'));
    }

    public function edit(ServerRequestInterface $request)
    {
        $errors = [];
        $item = $this->postTable->find((int)$request->getAttribute('id'));
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->postTable->update($item->id, $params);
                $this->flash->success('L\'article à bien été modifié');
                return $this->redirect('admin.post.edit', ['id' => $item->id]);
            }
            $errors = $validator->getErrors();
            $params['id'] = $item->id;
            $item = $params;
            $this->flash->error('Le système de modification à rencontrée une ou plusieurs erreurs');
        }
        return $this->renderer->render('@blog/admin/edit', compact('item', 'errors'));
    }

    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $this->postTable->delete($request->getAttribute('id'));
        return $this->redirect('blog.admin.posts');
    }

    private function getParams(ServerRequestInterface $request): array
    {
        $params = array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['title', 'slug', 'content', 'created_date']);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, [
            'apdated_date'  =>  date("Y-m-d H:i:s"),
            'view'          =>  0
        ]);
    }

    private function getValidator(ServerRequestInterface $request): Validator
    {
        return (new Validator($request->getParsedBody()))
            ->required('title', 'slug', 'content', 'created_date')
            ->length('title', 3, 250)
            ->length('slug', 3, 50)
            ->length('content', 10)
            ->datetime('created_date')
            ->slug('slug');
    }
}
