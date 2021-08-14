<?php

namespace Framework\Api\Stripe;

use Stripe\Card;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\Token;

class StripePurchase
{
    public function __construct(string $token)
    {
        Stripe::setApiKey($token);
    }

    /**
     * @throws ApiErrorException
     */
    public function getCardFromToken(string $token): Card
    {
        return Token::retrieve($token)->card;
    }

    /**
     * @throws ApiErrorException
     */
    public function getCustomer(string $customerID): Customer
    {
        return Customer::retrieve($customerID);
    }

    /**
     * @throws ApiErrorException
     */
    public function createCustomer(array $params): Customer
    {
        return Customer::create($params);
    }

    public function createCardForCustomer(Customer $customer, string $token)
    {
        return $customer->sources->create((['source' => $token]));
    }

    /**
     * @throws ApiErrorException
     */
    public function charge(array $params): Charge
    {
        return Charge::create($params);
    }
}
