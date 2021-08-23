<?php

namespace App\Shop\Actions;

use App\Blog\Table\PostTable;
use App\Shop\Entity\Product;
use App\Shop\Table\ProductsTable;
use App\Shop\Table\PurchaseTable;
use Framework\Auth;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProductShowAction
{
    private RendererInterface $renderer;
    private PostTable $postTable;
    private ProductsTable $productsTable;
    private string $stripeKey;
    private PurchaseTable $purchaseTable;
    private Auth $auth;

    public function __construct(
        RendererInterface $renderer,
        PostTable $postTable,
        ProductsTable $productsTable,
        PurchaseTable $purchaseTable,
        Auth $auth,
        string $stripeKey
    ) {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->productsTable = $productsTable;
        $this->stripeKey = $stripeKey;
        $this->purchaseTable = $purchaseTable;
        $this->auth = $auth;
    }

    /**
     * @throws NoRecordException
     */
    public function __invoke(ServerRequestInterface $request): string
    {
        $slug = $request->getAttribute('slug');
        $product = $this->productsTable->findBy('slug', $slug);
        $famousPosts = $this->famous();
        $newPosts = $this->new();
        $stripeKey = $this->stripeKey;
        $isDownloaded = $this->isDownloaded($product);
        return $this->renderer->render('@shop/show', compact(
            'product',
            'stripeKey',
            'famousPosts',
            'newPosts',
            'isDownloaded'
        ));
    }

    private function famous(): array
    {
        return $this->postTable->findPostsByField("view");
    }

    private function new(): array
    {
        return $this->postTable->findPostsByField("created_date");
    }

    private function isDownloaded(Product $product): bool
    {
        $isDownloaded = false;
        $user = $this->auth->getUser();
        if ($user !== null && $this->purchaseTable->findFor($product, $user) !== null) {
            $isDownloaded = true;
        }
        return $isDownloaded;
    }
}
