<?php

namespace App\Shop\Table;

use App\Shop\Entity\Product;
use Framework\Database\Table;

class ProductsTable extends Table
{
    protected ?string $entity = Product::class;

    protected string $table = "products";

    protected function findQuery(): string
    {
        return "SELECT * FROM $this->table WHERE id = ?";
    }
}
