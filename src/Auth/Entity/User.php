<?php

namespace App\Auth\Entity;

use Framework\Auth\User as UserInterface;

class User implements UserInterface
{
    public int $id;

    public string $username;

    public string $email;

    public string $password;

    public string $first_name;

    public string $last_name;

    public ?string $birth_date;

    public ?string $country;

    public ?string $city;

    public ?string $address;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function roles(): array
    {
        return [];
    }
}
