<?php

namespace App\Shop\Table;

use App\Auth\Entity\User;
use App\Shop\Entity\Product;
use App\Shop\Entity\Purchase;
use Framework\Database\Table;

class PurchaseTable extends Table
{
    protected ?string $entity = Purchase::class;

    protected string $table = 'purchases';

    public function findFor(Product $product, User $user): ?Purchase
    {
        $query = "SELECT * FROM $this->table WHERE product_id = :product AND user_id = :user";
        $result = $this->getPdo()->prepare($query);
        $result->execute([
            'product'   =>  $product->getId(),
            'user'      =>  $user->id
        ]);
        $result->setFetchMode(\PDO::FETCH_CLASS, Purchase::class);
        $return = $result->fetch();
        if ($return === false) {
            return null;
        }
        return $return;
    }
}
