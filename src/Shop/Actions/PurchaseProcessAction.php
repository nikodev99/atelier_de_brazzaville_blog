<?php

namespace App\Shop\Actions;

use App\Shop\Entity\Product;
use App\Shop\Exception\AlreadyPurchasedException;
use App\Shop\PurchaseProduct;
use App\Shop\Table\ProductsTable;
use Framework\Actions\RouterAwareAction;
use Framework\Auth;
use Framework\Database\NoRecordException;
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
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
    private SessionInterface $session;

    public function __construct(
        ProductsTable $productsTable,
        PurchaseProduct $purchaseProduct,
        Auth $auth,
        Router $router,
        SessionInterface $session,
        FlashService $flash
    ) {
        $this->productsTable = $productsTable;
        $this->purchaseProduct = $purchaseProduct;
        $this->auth = $auth;
        $this->router = $router;
        $this->flash = $flash;
        $this->session = $session;
    }

    /**
     * @throws NoRecordException|ApiErrorException
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        /** @var Product $product */
        $product = $this->productsTable->find((int)$request->getAttribute('id'));
        $quantity = (int)$request->getParsedBody()['quantity'];
        try {
            $session = $this->purchaseProduct->process($request, $this->router, $product, $this->auth->getUser(), $quantity);
            $this->flash->success('Merci pour votre achat');
            $this->session->set('checkout_params', [
                'checkout_id'   =>  $session->id,
                'product_id'    =>  $product->getId(),
                'quantity'      =>  $quantity
            ]);
            return new RedirectResponse($session->url);
        } catch (AlreadyPurchasedException $e) {
            $this->flash->info("Vous avez déjà acheter le produit " . $product->getName());
            return $this->redirect('shop.show', ['slug' => $product->getSlug()]);
        }
    }
}
