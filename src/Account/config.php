<?php

use App\Account\Entity\User;

return [
    'account.signup'    =>  '/inscription',
    'account.profile'   =>  '/profil',
    'account.history'   =>  '/profil/history',
    'account.edit'      =>  '/profil/edit',
    'auth.entity'       => User::class
];
