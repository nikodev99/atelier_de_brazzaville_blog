<?php

namespace Framework\Auth;

use Framework\Auth;
use Framework\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoleMiddleware implements MiddlewareInterface
{
    private Auth $auth;
    private string $role;

    public function __construct(Auth $auth, string $role)
    {
        $this->auth = $auth;
        $this->role = $role;
    }

    /**
     * @throws ForbiddenException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var User|null $user */
        $user = $this->auth->getUser();
        if ($user && !in_array($this->role, $user->roles())) {
            return new RedirectResponse('/profil');
        }
        if (is_null($user) || !in_array($this->role, $user->roles())) {
            throw new ForbiddenException();
        }
        return $handler->handle($request);
    }
}
