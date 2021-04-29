<?php

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class CategoryCrudAction extends CrudAction
{
    protected string $viewPath = "@blog/admin/categories";

    protected string $routePrefix = "admin.post.category";

    protected array $success_messages = [
        'create'    =>  "Nouvelle catégorie ajoutée avec succès !",
        'edit'      =>  "Catégorie modifiée avec succès !",
        'delete'    =>  "Catégorie supprimée avec succès !"
    ];

    public function __construct(RendererInterface $renderer, Router $router, CategoryTable $table, FlashService $flash)
    {
        parent::__construct($renderer, $router, $table, $flash);
    }

    protected function getParams(ServerRequestInterface $request, $item = null): array
    {
        return array_merge(
            array_filter($request->getParsedBody(), function ($key) {
                return in_array($key, ['name', 'slug']);
            }, ARRAY_FILTER_USE_KEY),
            [
                'created_date'  =>  date("Y-m-d H:i:s"),
                'updated_date'  =>  date("Y-m-d H:i:s"),
            ]
        );
    }

    protected function getValidator(ServerRequestInterface $request): Validator
    {
        return parent::getValidator($request)
            ->required('name', 'slug')
            ->length('name', 3, 250)
            ->length('slug', 3, 50)
            ->slug('slug');
    }
}
