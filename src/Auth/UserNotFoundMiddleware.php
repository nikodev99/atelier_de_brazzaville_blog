<?php

namespace App\Auth;

use Framework\Auth\ForbiddenException;
use Framework\Database\NoRecordException;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UserNotFoundMiddleware implements MiddlewareInterface
{
    private FlashService $flashService;

    /**
     * ForbiddenMiddleware constructor.
     * @param FlashService $flashService
     */
    public function __construct(FlashService $flashService)
    {
        $this->flashService = $flashService;
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (NoRecordException $f) {
            return $this->redirectToLogin($request);
        }
    }

    private function redirectToLogin(ServerRequestInterface $request): ResponseInterface
    {
        $this->flashService->error('Identifiant ou mot de passe incorrect');
        return new RedirectResponse($request->getUri()->getPath());
    }
}
