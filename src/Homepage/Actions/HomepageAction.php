<?php

namespace App\Homepage\Actions;

use Framework\Renderer\RendererInterface;

class HomepageAction
{


    private RendererInterface $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke()
    {
        return $this->index();
    }

    public function index(): string
    {
        return $this->renderer->render('@homepage/index');
    }
}
