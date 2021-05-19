<?php

namespace Framework\Auth;

use Framework\Auth;

class RoleMiddlewareFactory
{
    private Auth $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function makeForRole(string $role): RoleMiddleware
    {
        return new RoleMiddleware($this->auth, $role);
    }
}
