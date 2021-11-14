<?php

namespace App\Shop\Entity;

use DateTime;
use DateTimeZone;
use Error;
use Exception;

class Purchase
{
    private int $id;
    private int $user_id;
    private int $product_id;
    private float $price;
    private float $vat;
    private string $country;
    /** @var DateTime */
    private $created_at;
    private string $stripe_id;
    private int $success;
    private int $quantity;
    private string $invoice_number;
    private string $description;

    public function __construct()
    {
        if ($this->created_at) {
            $this->created_at = $this->getDateTime($this->created_at);
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->user_id = $userId;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->product_id;
    }

    /**
     * @param int $product_id
     */
    public function setProductId(int $product_id): void
    {
        $this->product_id = $product_id;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getVat(): float
    {
        return $this->vat;
    }

    /**
     * @param float $vat
     */
    public function setVat(float $vat): void
    {
        $this->vat = $vat;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    /**
     * @param $createdAt
     * @throws Exception
     */
    public function setCreatedAt($createdAt): void
    {
        if (is_string($createdAt)) {
            $this->created_at = new DateTime($createdAt);
        }
        $this->created_at = $createdAt;
    }

    /**
     * @return string
     */
    public function getStripeId(): string
    {
        return $this->stripe_id;
    }

    /**
     * @param string $stripe_id
     */
    public function setStripeId(string $stripe_id): void
    {
        $this->stripe_id = $stripe_id;
    }

    /**
     * @return int
     */
    public function getSuccess(): int
    {
        return $this->success;
    }

    /**
     * @param int $success
     */
    public function setSuccess(int $success): void
    {
        $this->success = $success;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber(): string
    {
        return $this->invoice_number;
    }

    /**
     * @param string $invoice_number
     */
    public function setInvoiceNumber(string $invoice_number): void
    {
        $this->invoice_number = $invoice_number;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    private function getDateTime($date): DateTime
    {
        try {
            return (new DateTime($date))->setTimezone(new DateTimeZone('Africa/Brazzaville'));
        } catch (Exception $e) {
            throw new Error("Type of date error: " . $e->getMessage());
        }
    }
}
