<?php

namespace App\Shop\Table;

use App\Auth\Entity\User;
use Framework\Database\Table;

class StripeUserTable extends Table
{
    protected string $table = 'users_table';

    public function findCustomerForUser(User $user): ?string
    {
        $query = $this->getPdo()->prepare("SELECT customer_id FROM $this->table WHERE user_id = :user");
        $query->execute(["user" => $user->id]);
        $obj = $query->fetch();
        return is_bool($obj) ? null : $obj->customer_id;
    }
}
