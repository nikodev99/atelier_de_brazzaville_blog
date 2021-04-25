<?php

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class CategoryShowAction
{
    private RendererInterface $renderer;

    private PostTable $postTable;

    private CategoryTable $categoryTable;

    public function __construct(RendererInterface $renderer, PostTable $postTable, CategoryTable $categoryTable)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->categoryTable = $categoryTable;
    }

    /**
     * @throws NoRecordException
     */
    public function __invoke(ServerRequestInterface $request): string
    {
        $category = $this->categoryTable->findBy('slug', $request->getAttribute("slug"));
        $params = $request->getQueryParams();
        $posts = $this->postTable->findPaginatedPublic(6, $params["p"] ?? 1, $category->id);
        $categories = $this->categoryTable->findAll();
        $newPosts = $this->postTable->findPostsByField("created_date");
        $famousPosts = $this->postTable->findPostsByField("view");
        return $this->renderer->render("@blog/index", compact("categories", "posts", "category", 'newPosts', 'famousPosts'));
    }
}
