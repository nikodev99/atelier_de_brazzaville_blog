<?php

namespace App\Shop\Table;

use App\Auth\Entity\User;
use App\Shop\Entity\Product;
use App\Shop\Entity\Purchase;
use Framework\Database\Table;
use PDO;
use stdClass;

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
        $query = "SELECT pp.*, p.name, p.slug, p.image, p.description
                    FROM purchases AS pp LEFT JOIN products AS p ON pp.product_id = p.id WHERE pp.user_id = :user ORDER BY pp.created_at DESC 
                    ";
        $result = $this->getPdo()->prepare($query);
        $result->execute([
            'user'  =>  $user->id
        ]);
        if (is_bool($result)) {
            return null;
        }
        return $result->fetchAll();
    }

    public function findWithProduct(int $purchaseId): ?Purchase
    {
        $query = "SELECT p.*, pr.name, pr.price AS ht, pr.image
                    FROM purchases AS p LEFT JOIN products AS pr ON p.product_id = pr.id WHERE p.id = :id 
                    ";
        $result = $this->getPdo()->prepare($query);
        $result->execute(['id' => $purchaseId]);
        if (is_bool($result)) {
            return null;
        }
        $this->checkEntity($result);
        return $result->fetch();
    }

    public function getDayIncome()
    {
        return $this->getIncome("DAY");
    }

    public function getWeekIncome()
    {
        return $this->getIncome("WEEK");
    }

    public function getMonthIncome()
    {
        return $this->getIncome("MONTH");
    }

    public function getYearIncome()
    {
        return $this->getIncome("YEAR");
    }

    public function getIncome(string $interval)
    {
        $query = "SELECT SUM(price) FROM purchases WHERE created_at BETWEEN DATE_SUB(NOW(), INTERVAL 1 $interval) AND NOW()";
        $result = $this->getPdo()->prepare($query);
        $result->execute();
        return $result->fetchColumn();
    }

    protected function paginationQuery(bool $limit = false, int $dataLimit = 3): string
    {
        return "SELECT u.*, p.name FROM purchases as u JOIN products p on u.product_id = p.id ORDER BY u.created_at DESC LIMIT 10";
    }
}
