<?php

namespace Framework;

use Framework\Auth\User;

interface Auth
{
    /**
     * @return User|null
     */
    public function getUser(): ?User;
}
