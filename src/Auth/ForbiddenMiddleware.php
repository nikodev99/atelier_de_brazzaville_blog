<?php

namespace App\Auth;

use Framework\Auth\ForbiddenException;
use Framework\Auth\User;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TypeError;

class ForbiddenMiddleware implements MiddlewareInterface
{
    private string $loginPath;

    private SessionInterface $session;

    /**
     * ForbiddenMiddleware constructor.
     * @param string $loginPath
     * @param SessionInterface $session
     */
    public function __construct(string $loginPath, SessionInterface $session)
    {
        $this->loginPath = $loginPath;
        $this->session = $session;
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ForbiddenException | TypeError $f) {
            return $this->redirectToLogin($request);
        }
    }

    private function redirectToLogin(ServerRequestInterface $request): ResponseInterface
    {
        $this->session->set('auth.redirect', $request->getUri()->getPath());
        (new FlashService($this->session))->error('Veuillez vous connecter pour accéder à cette page');
        return new RedirectResponse($this->loginPath);
    }
}
