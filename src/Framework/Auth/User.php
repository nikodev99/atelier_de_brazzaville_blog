<?php

namespace Framework\Auth;

use Framework\Auth;

interface User
{
    /**
     * @return string
     */
    public function getUsername(): string;

    /**
     * @return array|string[]
     */
    public function roles(): array;
}
