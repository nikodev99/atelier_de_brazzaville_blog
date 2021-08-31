<?php

namespace Framework\Api\Stripe;

use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Stripe\Exception\ApiConnectionException;

class ApiConnectionMiddleware implements MiddlewareInterface
{
    private SessionInterface $session;

    /**
     * ForbiddenMiddleware constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ApiConnectionException $f) {
            return $this->redirectToLogin();
        }
    }

    private function redirectToLogin(): ResponseInterface
    {
        (new FlashService($this->session))->error('Veuillez vous connecter pour effectuer le payment');
        return new RedirectResponse('/boutique-ephemere');
    }
}
