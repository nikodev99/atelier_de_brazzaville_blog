<?php

namespace App\Shop\Entity;

use DateTime;
use Exception;

class Purchase
{
    private int $id;
    private int $userId;
    private int $productId;
    private float $price;
    private float $vat;
    private string $country;
    private ?DateTime $createdAt;
    private string $stripeId;

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
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @param int $productId
     */
    public function setProductId(int $productId): void
    {
        $this->productId = $productId;
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
        return $this->createdAt;
    }

    /**
     * @param $createdAt
     * @throws Exception
     */
    public function setCreatedAt($createdAt): void
    {
        if (is_string($createdAt)) {
            $this->createdAt = new DateTime($createdAt);
        }
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getStripeId(): string
    {
        return $this->stripeId;
    }

    /**
     * @param string $stripeId
     */
    public function setStripeId(string $stripeId): void
    {
        $this->stripeId = $stripeId;
    }
}
