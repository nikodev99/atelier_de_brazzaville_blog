<?php

namespace App\Shop\Table;

use App\Shop\Entity\Product;
use Framework\Database\Table;

class StripeProductsTable extends Table
{
    protected string $table = 'stripe_products';

    public function findProductForProduct(Product $product)
    {
        $query = $this->getPdo()->prepare("SELECT stripe_product_id FROM stripe_products WHERE product_id = :product");
        $query->execute(["product" => $product->getId()]);
        $obj = $query->fetch();
        return is_bool($obj) ? null : $obj->stripe_product_id;
    }
}
