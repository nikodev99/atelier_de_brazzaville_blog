<?php

namespace App\Shop\Actions;

use App\Auth\Entity\User;
use App\Shop\Entity\Product;
use App\Shop\Table\ProductsTable;
use App\Shop\Table\PurchaseTable;
use Framework\Auth;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Framework\Session\SessionInterface;
use Mpociot\VatCalculator\VatCalculator;
use Psr\Http\Message\ServerRequestInterface;

class CheckoutAction
{
    private RendererInterface $renderer;
    private ProductsTable $productsTable;
    private Auth $auth;
    private SessionInterface $session;
    private PurchaseTable $purchaseTable;

    public function __construct(
        RendererInterface $renderer,
        Auth $auth,
        SessionInterface $session,
        ProductsTable $productsTable,
        PurchaseTable $purchaseTable
    ) {
        $this->renderer = $renderer;
        $this->productsTable = $productsTable;
        $this->auth = $auth;
        $this->session = $session;
        $this->purchaseTable = $purchaseTable;
    }

    /**
     * @throws NoRecordException
     */
    public function __invoke(ServerRequestInterface $request): string
    {
        if (strpos($request->getUri()->getPath(), "success")) {
            return $this->success();
        } else {
            return $this->cancel();
        }
    }

    /**
     * @throws NoRecordException
     */
    private function success(): string
    {
        $params = $this->session->get('checkout_params');

        $productID = $params['product_id'];
        /** @var Product $product */
        $product = $this->productsTable->find($productID);

        /** @var User $user */
        $user = $this->auth->getUser();
        $vat = new VatCalculator();
        $vatRate = $vat->getTaxRateForCountry($user->country);
        $grossPrice = $vat->calculate($product->getPrice(), $user->country);

        $sessionID = $params["checkout_id"];
        $quantity = $params['quantity'];

        $records = $this->purchaseTable->count();
        $invoice = "000" . ($records + 1);

        $description = "Achat de {$product->getName()} sur atelier-brazzaville.com";

        $this->productsTable->update($productID, ['quantity' => ($product->getQuantity() - $quantity)]);

        $this->purchaseTable->add([
            "user_id"       =>  $user->id,
            "product_id"    =>  $product->getId(),
            "price"         =>  $grossPrice,
            "vat"           =>  $vatRate,
            "country"       =>  $user->country,
            "created_at"    =>  date("Y-m-d H:i:s"),
            "stripe_id"     =>  $sessionID,
            "success"       =>  1,
            'quantity'      =>  $quantity,
            'invoice_number' =>  $invoice,
            "description"   =>  $description
        ]);

        $this->session->delete("checkout_params");

        return $this->renderer->render("@shop/success");
    }

    private function cancel(): string
    {
        return $this->renderer->render("@shop/cancel");
    }
}
