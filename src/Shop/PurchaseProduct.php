<?php

namespace App\Shop;

use App\Auth\Entity\User;
use App\Shop\Entity\Product;
use App\Shop\Entity\Purchase;
use App\Shop\Exception\AlreadyPurchasedException;
use App\Shop\Table\PurchaseTable;
use App\Shop\Table\StripePriceTable;
use App\Shop\Table\StripeProductsTable;
use App\Shop\Table\StripeUserTable;
use Framework\Api\Stripe\StripePurchase;
use Framework\Router;
use Mpociot\VatCalculator\VatCalculator;
use Psr\Http\Message\ServerRequestInterface;
use Stripe\Card;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Price;

class PurchaseProduct
{
    private PurchaseTable $purchaseTable;
    private StripePurchase $stripe;
    private StripeUserTable $stripeUserTable;
    private StripeProductsTable $productsTable;
    private StripePriceTable $priceTable;
    public function __construct(
        PurchaseTable $purchaseTable,
        StripePurchase $stripe,
        StripeUserTable $stripeUserTable,
        StripeProductsTable $productsTable,
        StripePriceTable $priceTable
    ) {
        $this->purchaseTable = $purchaseTable;
        $this->stripe = $stripe;
        $this->stripeUserTable = $stripeUserTable;
        $this->productsTable = $productsTable;
        $this->priceTable = $priceTable;
    }

    /**
     * @throws AlreadyPurchasedException
     * @throws ApiErrorException
     */
    public function process(ServerRequestInterface $request, Router $router, Product $product, User $user, int $quantity): Session
    {
        //HTTP_HOST
        $host = 'http://' . $request->getServerParams()['HTTP_HOST'];

        //Checks that the user hasn't already buy the specific product
        $checkProduct = $this->purchaseTable->findFor($product, $user);
        if ($checkProduct instanceof Purchase) {
            throw new AlreadyPurchasedException();
        }

        //Create or retrieve a customer
        $customer = $this->findCustomerForUser($user);

        //Calculate of the gross price including vat of a specific country
        $vat = new VatCalculator();
        $clientCountry = $user->country;
        $grossPrice = floor($vat->calculate($product->getPrice(), $clientCountry));

        //Create a product and a price
        $stripeProduct = $this->findProductForProduct($product, $host);
        $stripePrice = $this->findPriceForProduct($product, $stripeProduct, (int)$grossPrice);

        //Charge the customer
        return $this->stripe->charge([
            "payment_intent_data"   =>  [
                "setup_future_usage"    =>  'off_session'
            ],
            "customer_email"    =>  $customer->email,
            "payment_method_types"    =>  ['card'],
            "line_items"    =>  [
                [
                    "price"     =>  $stripePrice->id,
                    "quantity"  =>  $quantity
                ]
            ],
            "mode"  =>  "payment",
            "success_url"   =>  $host . $router->setUri('shop.payment.success', [], ['p' => $product->getId()]),
            "cancel_url"    =>  $host . $router->setUri('shop.payment.cancel')
        ]);
    }

    /**
     * @param Customer $customer
     * @param Card $card
     * @return null|Card
     * @throws ApiErrorException
     */
    #private function getMatchingCard(Customer $customer, Card $card): ?Card
    #{
    #   $client = $this->stripe->getClient()->customers->allSources($customer->id, ['object' => 'card']);
    #   /** @var Card[] $cards */
    #    $cards = $client->data;
    #    foreach ($cards as $datum) {
    #        if ($datum->fingerprint === $card->fingerprint) {
    #            return $datum;
    #        }
    #    }
    #    return null;
    #}

    /**
     * @param User $user
     * @return Customer
     * @throws ApiErrorException
     */
    private function findCustomerForUser(User $user): Customer
    {
        $customerID = $this->stripeUserTable->findCustomerForUser($user);
        if (!is_null($customerID)) {
            $customer = $this->stripe->getCustomer($customerID);
        } else {
            $customer = $this->stripe->createCustomer([
                "email"     =>  $user->email,
                "name"      =>  $user->first_name
            ]);
            $this->stripeUserTable->add([
                "user_id"   =>  $user->id,
                "customer_id"   =>  $customer->id,
                "created_at"    =>  date("Y-m-d H:i:s")
            ]);
        }
        return $customer;
    }


    /**
     * @throws ApiErrorException
     */
    private function findProductForProduct(Product $product, string $host): \Stripe\Product
    {
        $stripeProductID = $this->productsTable->findProductForProduct($product);
        if (!is_null($stripeProductID)) {
            $stripeProduct = $this->stripe->getProduct($stripeProductID);
        } else {
            $stripeProduct = $this->stripe->createProduct([
                'name'  =>  $product->getName(),
                "description"   =>  "Achat de {$product->getName()} sur atelier-brazzaville.com",
                #TODO Ajouter les images à envoyé au checkout
                //"images"    =>  [$host . '/' . $product->getMain()]
            ]);
            $this->productsTable->add([
                "product_id"    =>  $product->getId(),
                "stripe_product_id" => $stripeProduct->id,
                "created_at"    =>  date("Y-m-d H:i:s")
            ]);
        }
        return $stripeProduct;
    }

    /**
     * @throws ApiErrorException
     */
    private function findPriceForProduct(Product $product, \Stripe\Product $stripeProduct, int $grossPrice): Price
    {
        $priceID = $this->priceTable->findPriceForProduct($product);
        if (!is_null($priceID)) {
            $price = $this->stripe->getPrice($priceID);
        } else {
            $price = $this->stripe->createPrice([
                'product'   =>  $stripeProduct->id,
                'unit_amount'   =>  $grossPrice * 100,
                'currency'  =>  'eur',
            ]);
            $this->priceTable->add([
                'product_id'    =>  $product->getId(),
                'price_id'      =>  $price->id,
                'price'         =>  $grossPrice * 100,
                'created_at'    =>  date("Y-m-d H:i:s")
            ]);
        }
        return $price;
    }
}
