<?php

namespace Framework\Api\Stripe;

use Stripe\Card;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;
use Stripe\StripeClient;

class StripePurchase
{
    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
        Stripe::setApiKey($this->token);
    }

    public function getClient(): StripeClient
    {
        return new StripeClient($this->token);
    }

    /**
     * @throws ApiErrorException
     */
    public function getCustomer(string $customerID): Customer
    {
        return $this->getClient()->customers->retrieve($customerID);
    }

    /**
     * @throws ApiErrorException
     */
    public function createCustomer(array $params): Customer
    {
        return $this->getClient()->customers->create($params);
    }

    /**
     * @throws ApiErrorException
     */
    public function createCardForCustomer(Customer $customer, string $token): Card
    {
        return $this->getClient()->customers->createSource($customer->id, ['source' => $token]);
        //return $customer->sources->create(['source' => $token]);
    }

    /**
     * @throws ApiErrorException
     */
    public function createProduct(array $params): Product
    {
        return Product::create($params);
    }

    /**
     * @throws ApiErrorException
     */
    public function getProduct(string $productID): Product
    {
        return Product::retrieve($productID);
    }

    /**
     * @throws ApiErrorException
     */
    public function createPrice(array $params): Price
    {
        return Price::create($params);
    }

    /**
     * @throws ApiErrorException
     */
    public function getPrice(string $priceID): Price
    {
        return Price::retrieve($priceID);
    }

    /**
     * @throws ApiErrorException
     */
    public function charge(array $params): Session
    {
        return Session::create($params);
    }
}
