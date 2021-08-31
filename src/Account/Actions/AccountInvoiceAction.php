<?php

namespace App\Account\Actions;

use App\Blog\Actions\PostIndexAction;
use App\Blog\Table\PostTable;
use App\Shop\Table\PurchaseTable;
use Framework\Auth;
use Framework\Renderer\RendererInterface;

class AccountInvoiceAction
{

    private RendererInterface $renderer;
    private Auth $auth;
    private PostTable $postTable;
    private PurchaseTable $purchaseTable;

    public function __construct(RendererInterface $renderer, Auth $auth, PostTable $postTable, PurchaseTable $purchaseTable)
    {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->postTable = $postTable;
        $this->purchaseTable = $purchaseTable;
    }

    public function __invoke(): string
    {
        $user = $this->auth->getUser();
        $post = new PostIndexAction($this->renderer, $this->postTable);
        $productsPurchased = $this->purchaseTable->findByUser($user);
        $famousPosts = $post->famous();
        $newPosts = $post->new();
        return $this->renderer->render('@account/invoice', compact('user', 'productsPurchased', 'famousPosts', 'newPosts'));
    }
}
