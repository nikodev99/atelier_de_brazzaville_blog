<?php

namespace App\Shop;

use App\Auth\Entity\User;
use App\Shop\Entity\Product;
use App\Shop\Entity\Purchase;
use App\Shop\Exception\AlreadyPurchasedException;
use App\Shop\Table\PurchaseTable;
use App\Shop\Table\StripeUserTable;
use Framework\Api\Stripe\StripePurchase;
use Mpociot\VatCalculator\VatCalculator;
use Stripe\Card;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;

class PurchaseProduct
{
    private PurchaseTable $purchaseTable;
    private StripePurchase $stripe;
    private StripeUserTable $stripeUserTable;

    public function __construct(PurchaseTable $purchaseTable, StripePurchase $stripe, StripeUserTable $stripeUserTable)
    {
        $this->purchaseTable = $purchaseTable;
        $this->stripe = $stripe;
        $this->stripeUserTable = $stripeUserTable;
    }

    /**
     * @throws AlreadyPurchasedException
     * @throws ApiErrorException
     */
    public function process(Product $product, User $user, string $token)
    {
        //Checks that the user hasn't already buy the specific product
        $checkProduct = $this->purchaseTable->findFor($product, $user);
        if ($checkProduct instanceof Purchase) {
            throw new AlreadyPurchasedException();
        }

        //Calculate of the gross price including vat of a specific country
        $card = $this->stripe->getCardFromToken($token);
        $vat = new VatCalculator();
        $vatRate = $vat->getTaxRateForCountry($card->country);
        $grossPrice = floor($vat->calculate($product->getPrice(), $card->country));

        //Create or retrieve a customer
        $customer = $this->findCustomerForUser($user, $token);

        //Check if the customer has already used this card on the website
        $card = $this->getMatchingCard($customer, $card);
        if (is_null($card)) {
            $card = $this->stripe->createCardForCustomer($customer, $token);
        }

        //Charge the customer

        $charge = $this->stripe->charge([
            "amount"    =>  $grossPrice,
            "currency"  =>  "eur",
            "source"    =>  $card->id,
            "customer"    =>  $customer->id,
            "description"   =>  "Achat de {$product->getName()} sur atelier-brazzaville.com"
        ]);

        //Saving the transaction
        $this->purchaseTable->add([
            "user_id"       =>  $user->id,
            "product_id"    =>  $product->getId(),
            "price"         =>  $grossPrice,
            "vat"           =>  $vatRate,
            "country"       =>  $card->country,
            "created_at"    =>  date("Y-m-d H:i:s"),
            "stripe_id"     =>  $charge->id
        ]);
    }

    /**
     * @param Customer $customer
     * @param Card $card
     * @return Card|null
     */
    private function getMatchingCard(Customer $customer, Card $card): ?Card
    {
        //dd($customer->sources->data);
        foreach ($customer->sources->data as $datum) {
            if ($datum->fingerprint === $card->fingerprint) {
                return $datum;
            }
        }
        return null;
    }

    /**
     * @param User $user
     * @param string $token
     * @return Customer
     * @throws ApiErrorException
     */
    private function findCustomerForUser(User $user, string $token): Customer
    {
        $customerID = $this->stripeUserTable->findCustomerForUser($user);
        if (!is_null($customerID)) {
            $customer = $this->stripe->getCustomer($customerID);
        } else {
            $customer = $this->stripe->createCustomer([
                "email"     =>  $user->email,
                "source"    =>  $token
            ]);
            $this->stripeUserTable->add([
                "user_id"   =>  $user->id,
                "customer_id"   =>  $customer->id,
                "created_at"    =>  date("Y-m-d H:i:s")
            ]);
        }
        return $customer;
    }
}
