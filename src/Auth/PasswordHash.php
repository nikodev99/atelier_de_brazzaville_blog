<?php

namespace App\Auth;

class PasswordHash
{
    private const PEPPER = "c1isvFdxMDdmjOlvxpecFw";

    public static function hash(string $passwordToHash): string
    {
        $pwd_prepared = hash_hmac('sha256', $passwordToHash, self::PEPPER, true);
        return password_hash($pwd_prepared, PASSWORD_ARGON2ID);
    }

    public static function verify(string $passwordToVerify, string $hashedPassword): bool
    {
        $pwd = hash_hmac('sha256', $passwordToVerify, self::PEPPER, true);
        return password_verify($pwd, $hashedPassword);
    }
}
