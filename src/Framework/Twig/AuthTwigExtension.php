<?php

namespace Framework\Twig;

use Framework\Auth;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AuthTwigExtension extends AbstractExtension
{
    private Auth $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('current_user', [$this->auth, 'getUser'])
        ];
    }
}
