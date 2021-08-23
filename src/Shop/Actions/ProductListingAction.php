<?php

namespace App\Shop\Actions;

use App\Blog\Table\PostTable;
use App\Shop\Table\ProductsTable;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProductListingAction
{

    private RendererInterface $renderer;
    private ProductsTable $productsTable;
    private PostTable $postTable;

    public function __construct(RendererInterface $renderer, ProductsTable $productsTable, PostTable $postTable)
    {
        $this->renderer = $renderer;
        $this->productsTable = $productsTable;
        $this->postTable = $postTable;
    }

    public function __invoke(ServerRequestInterface $request): string
    {
        $params = $request->getQueryParams();
        $page = $params['p'] ?? 1;
        $products = $this->productsTable->findPublic()->paginate(9, $page);
        $famousPosts = $this->famous();
        $newPosts = $this->new();
        return $this->renderer->render("@shop/index", compact('products', 'page', 'famousPosts', 'newPosts'));
    }

    private function famous(): array
    {
        return $this->postTable->findPostsByField("view");
    }

    private function new(): array
    {
        return $this->postTable->findPostsByField("created_date");
    }
}
