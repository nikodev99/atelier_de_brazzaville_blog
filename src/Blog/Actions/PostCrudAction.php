<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\PostImageUpload;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use DateTime;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Framework\Database\NoRecordException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostCrudAction extends CrudAction
{
    protected string $viewPath = "@blog/admin/posts";

    protected string $routePrefix = "admin.post";

    private PostTable $table;

    private CategoryTable $categoryTable;

    private PostImageUpload $imageUpload;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $table,
        FlashService $flash,
        CategoryTable $categoryTable,
        PostImageUpload $imageUpload
    ) {
        $this->categoryTable = $categoryTable;
        parent::__construct($renderer, $router, $table, $flash);
        $this->imageUpload = $imageUpload;
        $this->table = $table;
    }

    protected function formParam(array $params): array
    {
        $params['categories'] = $this->categoryTable->findList();
        $params['categories']['12234567'] = "categorie fake";
        return $params;
    }

    protected function getNewEntity(): Post
    {
        $post = new Post();
        $post->created_date = new DateTime();
        return $post;
    }

    protected function getParams(ServerRequestInterface $request, $item = null): array
    {
        $clientFilename = $request->getUploadedFiles()['image']->getClientFilename();
        $params = $request->getParsedBody();
        if (!empty($clientFilename)) {
            $params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
            if (!is_null($item)) {
                $params['image'] = $this->imageUpload->upload($params["image"], $item->image);
            } else {
                $params['image'] = $this->imageUpload->upload($params["image"]);
            }
        }
        $params = array_filter($params, function ($key) {
                return in_array($key, ['title', 'slug', 'content', 'created_date', 'category_id', 'image']);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, [
            'apdated_date'  =>  date("Y-m-d H:i:s"),
            'view'          =>  0,
        ]);
    }

    /**
     * @throws NoRecordException
     */
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $post = $this->table->find($request->getAttribute('id'));
        $this->imageUpload->delete($post->image);
        return parent::delete($request);
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
            ->extension('image', ['jpg', 'jpeg', 'png', 'gif'])
            ->exists('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo());
    }
}
