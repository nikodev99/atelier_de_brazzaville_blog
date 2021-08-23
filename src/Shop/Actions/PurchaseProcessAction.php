<?php

namespace App\Shop\Actions;

use App\Shop\Entity\Product;
use App\Shop\Exception\AlreadyPurchasedException;
use App\Shop\PurchaseProduct;
use App\Shop\Table\ProductsTable;
use Framework\Actions\RouterAwareAction;
use Framework\Auth;
use Framework\Database\NoRecordException;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Stripe\Exception\ApiErrorException;

class PurchaseProcessAction
{
    use RouterAwareAction;

    private ProductsTable $productsTable;
    private PurchaseProduct $purchaseProduct;
    private Auth $auth;
    private Router $router;
    private FlashService $flash;

    public function __construct(
        ProductsTable $productsTable,
        PurchaseProduct $purchaseProduct,
        Auth $auth,
        Router $router,
        FlashService $flash
    ) {
        $this->productsTable = $productsTable;
        $this->purchaseProduct = $purchaseProduct;
        $this->auth = $auth;
        $this->router = $router;
        $this->flash = $flash;
    }

    /**
     * @throws NoRecordException|ApiErrorException
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        /** @var Product $product */
        $product = $this->productsTable->find((int)$request->getAttribute('id'));
        $token = $request->getParsedBody()['stripeToken'];
        try {
            $this->purchaseProduct->process($product, $this->auth->getUser(), $token);
            $this->flash->success('Merci pour votre achat');
            return $this->redirect('account.history');
        } catch (AlreadyPurchasedException $e) {
            $this->flash->info("Vous avez déjà acheter le produit " . $product->getName());
            return $this->redirect('shop.show', ['slug' => $product->getSlug()]);
        }
    }
}
