<?php

use App\Auth\DatabaseAuth;
use App\Auth\Entity\User;
use App\Auth\ForbiddenMiddleware;
use App\Auth\Table\UserTable;
use Framework\Auth;
use Framework\Session\SessionInterface;

use function DI\autowire;
use function DI\create;
use function DI\factory;
use function DI\get;

return [
    'auth.login'    =>  '/login',
    'auth.logout'   =>  '/logout',
    'auth.entity'   =>  User::class,
    User::class     =>  factory(function (Auth $auth) {
        return $auth->getUser();
    })->parameter('auth', Auth::class),
    Auth::class     =>  autowire(DatabaseAuth::class),
    UserTable::class    =>  create()->constructor(get(PDO::class), get('auth.entity')),
    ForbiddenMiddleware::class  =>  create()->constructor(
        get('auth.login'),
        get(SessionInterface::class)
    )
];
