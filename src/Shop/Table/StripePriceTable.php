<?php

namespace App\Shop\Table;

use App\Shop\Entity\Product;
use Framework\Database\Table;

class StripePriceTable extends Table
{
    protected string $table = 'stripe_price';

    public function findPriceForProduct(Product $product): ?string
    {
        $query = $this->getPdo()->prepare("SELECT price_id FROM stripe_price WHERE product_id = :product");
        $query->execute(["product" => $product->getId()]);
        $obj = $query->fetch();
        return is_bool($obj) ? null : $obj->price_id;
    }
}
