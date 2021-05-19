<?php

namespace App\Account\Actions;

use App\Blog\Actions\PostIndexAction;
use App\Blog\Table\PostTable;
use Framework\Auth;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class AccountAction
{

    private RendererInterface $renderer;
    private Auth $auth;
    private PostTable $postTable;

    public function __construct(RendererInterface $renderer, Auth $auth, PostTable $postTable)
    {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->postTable = $postTable;
    }

    public function __invoke(ServerRequestInterface $request): string
    {
        $user = $this->auth->getUser();
        $post = new PostIndexAction($this->renderer, $this->postTable);
        $famousPosts = $post->famous();
        $newPosts = $post->new();
        return $this->renderer->render('@account/profile', compact('user', 'famousPosts', 'newPosts'));
    }
}
