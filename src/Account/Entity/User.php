<?php

namespace App\Account\Entity;

use App\Auth\Entity\User as AuthUser;

class User extends AuthUser
{
    /** @var string */
    private $role;

    public function roles(): array
    {
        return [$this->role];
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }
}
