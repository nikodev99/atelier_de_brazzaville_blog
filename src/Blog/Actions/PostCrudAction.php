<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use DateTime;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class PostCrudAction extends CrudAction
{
    protected string $viewPath = "@blog/admin/posts";

    protected string $routePrefix = "admin.post";

    private CategoryTable $categoryTable;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $table,
        FlashService $flash,
        CategoryTable $categoryTable
    ) {
        $this->categoryTable = $categoryTable;
        parent::__construct($renderer, $router, $table, $flash);
    }

    protected function formParam(array $params): array
    {
        $params['categories'] = $this->categoryTable->findList();
        $params['categories']['12234567'] = "categorie fake";
        return $params;
    }

    protected function getNewEntity()
    {
        $post = new Post();
        $post->created_date = new DateTime();
        return $post;
    }

    protected function getParams(ServerRequestInterface $request): array
    {
        $params = array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['title', 'slug', 'content', 'created_date', 'category_id']);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, [
            'apdated_date'  =>  date("Y-m-d H:i:s"),
            'view'          =>  0,
            'post_id'       =>  ''
        ]);
    }

    protected function getValidator(ServerRequestInterface $request): Validator
    {
        return parent::getValidator($request)
            ->required('title', 'slug', 'content', 'created_date', 'category_id')
            ->length('title', 3, 250)
            ->length('slug', 3, 50)
            ->length('content', 10)
            ->datetime('created_date')
            ->slug('slug')
            ->exists('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo());
    }
}
