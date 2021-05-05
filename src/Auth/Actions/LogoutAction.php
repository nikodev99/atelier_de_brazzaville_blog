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

    /**
     * LogoutAction constructor.
     * @param RendererInterface $renderer
     * @param DatabaseAuth $auth
     */
    public function __construct(RendererInterface $renderer, DatabaseAuth $auth)
    {
        $this->auth = $auth;
        $this->renderer = $renderer;
    }

    public function __invoke(ServerRequestInterface $request): RedirectResponse
    {
        $this->auth->logout();
        return new RedirectResponse('/');
    }
}
