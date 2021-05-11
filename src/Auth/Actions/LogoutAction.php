<?php

namespace App\Auth\Actions;

use App\Auth\DatabaseAuth;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;

class LogoutAction
{
    private RendererInterface $renderer;

    private DatabaseAuth $auth;

    private FlashService $flash;

    /**
     * LogoutAction constructor.
     * @param RendererInterface $renderer
     * @param DatabaseAuth $auth
     * @param FlashService $flash
     */
    public function __construct(RendererInterface $renderer, DatabaseAuth $auth, FlashService $flash)
    {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->flash = $flash;
    }

    public function __invoke(ServerRequestInterface $request): RedirectResponse
    {
        $this->auth->logout();
        $this->flash->success("Vous êtes désormais déconnecter");
        return new RedirectResponse('/');
    }
}
