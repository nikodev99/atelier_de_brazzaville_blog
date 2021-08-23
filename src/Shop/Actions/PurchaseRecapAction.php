<?php

namespace App\Shop\Actions;

use App\Shop\Table\ProductsTable;
use Framework\Api\Stripe\StripePurchase;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Mpociot\VatCalculator\VatCalculator;
use Psr\Http\Message\ServerRequestInterface;
use Stripe\Exception\ApiErrorException;

class PurchaseRecapAction
{
    private RendererInterface $renderer;
    private ProductsTable $productsTable;
    private StripePurchase $stripe;

    public function __construct(RendererInterface $renderer, ProductsTable $productsTable, StripePurchase $stripe)
    {
        $this->renderer = $renderer;
        $this->productsTable = $productsTable;
        $this->stripe = $stripe;
    }

    /**
     * @throws NoRecordException
     * @throws ApiErrorException
     */
    public function __invoke(ServerRequestInterface $request): string
    {
        $token = $request->getParsedBody()['stripeToken'];
        $card = $this->stripe->getCardFromToken($token);
        $vat = new VatCalculator();
        $vatRate = $vat->getTaxRateForCountry($card->country);

        $id = (int)$request->getAttribute('id');
        $product = $this->productsTable->find($id);
        $price = floor($vat->calculate($product->getPrice(), $card->country));

        return $this->renderer->render('@shop/recap', compact('product', 'card', 'vatRate', 'price', 'token'));
    }
}
