<?php

namespace App\Auth\Actions;

use Framework\Renderer\RendererInterface;

class LoginAction
{
    private RendererInterface $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke(): string
    {
        return $this->renderer->render('@auth/login', []);
    }
}
