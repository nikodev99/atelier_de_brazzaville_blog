<?php

namespace App\Shop\Entity;

use DateTime;
use Framework\Entity\Timestamp;

class Product
{
    use Timestamp;

    private int $id = 0;

    private string $name = "";

    private ?string $description = null;

    private ?string $slug = null;

    private float $price = 0;

    private ?string $image = null;

    private const PATH = '../../style/upload/products/';

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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     */
    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
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
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getThumb(): ?string
    {
        return  self::PATH . $this->getPhoto('thumb', 'blog_square_01.jpg');
    }

    public function getMain(): ?string
    {
        return  self::PATH . $this->getPhoto('main', 'blog_10.jpg');
    }

    public function getLeft(): ?string
    {
        return  self::PATH . $this->getPhoto('left', 'blog_masonry_01.jpg');
    }

    private function getPhoto(string $type, string $default): string
    {
        if (!is_null($this->image)) {
            ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);
            return $filename . "-$type.$extension";
        }
        return $default;
    }

    public function getPdf(): string
    {
        return "{$this->getId()}.pdf";
    }
}
