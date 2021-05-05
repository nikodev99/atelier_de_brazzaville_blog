<?php

use App\Auth\DatabaseAuth;
use App\Auth\Entity\User;
use App\Auth\ForbiddenMiddleware;
use Framework\Auth;
use Framework\Session\SessionInterface;

use function DI\autowire;
use function DI\create;
use function DI\factory;
use function DI\get;

return [
    'auth.login'    =>  '/login',
    'auth.logout'   =>  '/logout',
    User::class     =>  factory(function (Auth $auth) {
        return $auth->getUser();
    })->parameter('auth', Auth::class),
    Auth::class     =>  autowire(DatabaseAuth::class),
    ForbiddenMiddleware::class  =>  create()->constructor(
        get('auth.login'),
        get(SessionInterface::class)
    )
];
