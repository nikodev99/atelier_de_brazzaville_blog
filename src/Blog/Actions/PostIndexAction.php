<?php

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostIndexAction
{
    use RouterAwareAction;

    private RendererInterface $renderer;

    private PostTable $postTable;

    /**
     * @var CategoryTable
     */
    private CategoryTable $categoryTable;

    public function __construct(RendererInterface $renderer, PostTable $postTable, CategoryTable $categoryTable = null)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        if ($categoryTable) {
            $this->categoryTable = $categoryTable;
        }
    }

    public function __invoke(ServerRequestInterface $request): string
    {
        $params = $request->getQueryParams();
        $currentPage = $params['p'] ?? 1;
        if (strpos($request->getUri()->getPath(), "tendances")) {
            return $this->tendances($currentPage);
        }
        if (strpos($request->getUri()->getPath(), "article-a-la-une")) {
            return $this->newPosts($currentPage);
        }
        $posts = $this->postTable->findPaginatedPublic(6, $currentPage, 3);
        $categories = $this->categoryTable->findAll();
        $newPosts = $this->new();
        return $this->renderer->render('@blog/index', compact('posts', 'categories', 'newPosts'));
    }

    public function contact(array $errors = []): string
    {
        $famousPosts = $this->famous();
        $newPosts = $this->new();
        return $this->renderer->render("@blog/contact", compact('famousPosts', 'newPosts', 'errors'));
    }

    public function famous(): array
    {
        return $this->postTable->findPostsByField("view");
    }

    public function new(): array
    {
        return $this->postTable->findPostsByField("created_date");
    }

    private function tendances(int $current_page): string
    {
        $fPosts = $this->postTable->findPaginatedByField(9, $current_page, "view");
        $famousPosts = $this->famous();
        $newPosts = $this->new();
        return $this->renderer->render("@blog/famous_posts", compact('fPosts', 'famousPosts', 'newPosts'));
    }

    private function newPosts(int $current_page): string
    {
        $fPosts = $this->postTable->findPaginatedByField(9, $current_page, "created_date");
        $famousPosts = $this->famous();
        $newPosts = $this->new();
        return $this->renderer->render("@blog/new_posts", compact('fPosts', 'famousPosts', 'newPosts'));
    }
}
