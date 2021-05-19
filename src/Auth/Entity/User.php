<?php

namespace App\Auth\Entity;

use DateTime;
use DateTimeZone;
use Error;
use Exception;
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

    /**
     * @var string|DateTime
     */
    public $registration;

    public function __construct()
    {
        if ($this->registration) {
            $this->registration = $this->getDateTime($this->registration);
        }
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function roles(): array
    {
        return [];
    }

    private function getDateTime($date): DateTime
    {
        try {
            return (new DateTime($date))->setTimezone(new DateTimeZone('Africa/Brazzaville'));
        } catch (Exception $e) {
            throw new Error("Type of date error: " . $e->getMessage());
        }
    }
}
