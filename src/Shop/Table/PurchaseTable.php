<?php

namespace App\Shop\Table;

use App\Auth\Entity\User;
use App\Shop\Entity\Product;
use App\Shop\Entity\Purchase;
use Framework\Database\Table;
use PDO;

class PurchaseTable extends Table
{
    protected ?string $entity = Purchase::class;

    protected string $table = 'purchases';

    public function findFor(Product $product, User $user): ?Purchase
    {
        $query = "SELECT * FROM purchases WHERE product_id = :product AND user_id = :user";
        $result = $this->getPdo()->prepare($query);
        $result->execute([
            'product'   =>  $product->getId(),
            'user'      =>  $user->id
        ]);
        $result->setFetchMode(PDO::FETCH_CLASS, Purchase::class);
        $return = $result->fetch();
        if ($return === false) {
            return null;
        }
        return $return;
    }

    public function findByUser(User $user): ?array
    {
        $query = "SELECT pp.user_id, pp.product_id, pp.price, pp.created_at, pp.stripe_id, p.name, p.name, p.id, p.slug, p.image, p.description
                    FROM purchases AS pp LEFT JOIN products AS p ON pp.product_id = p.id WHERE pp.user_id = :user
                    ";
        $result = $this->getPdo()->prepare($query);
        $result->execute([
            'user'  =>  $user->id
        ]);
        if (is_bool($result)) {
            return null;
        }
        $this->checkEntity($result);
        return $result->fetchAll();
    }
}
