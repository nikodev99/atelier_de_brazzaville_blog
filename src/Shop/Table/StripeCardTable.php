<?php

namespace App\Shop\Table;

use App\Auth\Entity\User;
use Framework\Database\Table;

class StripeCardTable extends Table
{
    protected string $table = 'stripe_cards';

    public function findCardForUser(User $user): ?string
    {
        $query = $this->getPdo()->prepare("SELECT card_id FROM stripe_cards WHERE user_id = :user");
        $query->execute(["user" => $user->id]);
        $obj = $query->fetch();
        return is_bool($obj) ? null : $obj->card_id;
    }
}
