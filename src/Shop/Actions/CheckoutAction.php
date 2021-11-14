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
use Swift_Mailer;
use Swift_Message;

class CheckoutAction
{
    private RendererInterface $renderer;
    private ProductsTable $productsTable;
    private Auth $auth;
    private SessionInterface $session;
    private PurchaseTable $purchaseTable;
    private Swift_Mailer $mailer;
    private string $to;

    public function __construct(
        RendererInterface $renderer,
        Auth $auth,
        SessionInterface $session,
        ProductsTable $productsTable,
        PurchaseTable $purchaseTable,
        Swift_Mailer $mailer,
        string $to
    ) {
        $this->renderer = $renderer;
        $this->productsTable = $productsTable;
        $this->auth = $auth;
        $this->session = $session;
        $this->purchaseTable = $purchaseTable;
        $this->mailer = $mailer;
        $this->to = $to;
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

        $parameters = [
            "name"  =>  $user->first_name . " " . $user->last_name,
            "email" =>  $user->email,
            "address"   =>  $user->address,
            "product"   =>  $product->getName(),
            "price" =>  $product->getPrice(),
            "vat"   =>  $vatRate * 100,
            "amount"    =>  $grossPrice,
            "country"   =>  $user->country,
            "fret"  => "",
            "description"   =>  $product->getDescription(),
            "subject"   =>  "Achat sur latelierbrazzaville.com"
        ];
        $body = $this->renderer->render('@shop/mail.html', $parameters);
        $message = (new Swift_Message())
            ->setSubject($parameters['subject'])
            ->setBody($body, 'text/html', 'utf-8')
            ->setFrom('contact@latelierbrazzaville.com', 'latelierbrazzaville.com')
            ->setTo($this->to);
        $this->mailer->send($message);

        $this->session->delete("checkout_params");

        return $this->renderer->render("@shop/success");
    }

    private function cancel(): string
    {
        return $this->renderer->render("@shop/cancel");
    }
}
