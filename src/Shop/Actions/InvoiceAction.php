<?php

namespace App\Shop\Actions;

use App\Shop\Entity\Purchase;
use App\Shop\Table\PurchaseTable;
use Framework\Auth;
use Framework\Auth\ForbiddenException;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class InvoiceAction
{
    private RendererInterface $renderer;
    private PurchaseTable $purchaseTable;
    private Auth $auth;

    public function __construct(RendererInterface $renderer, PurchaseTable $purchaseTable, Auth $auth)
    {
        $this->renderer = $renderer;
        $this->purchaseTable = $purchaseTable;
        $this->auth = $auth;
    }

    /**
     * @throws ForbiddenException
     */
    public function __invoke(ServerRequestInterface $request): string
    {
        /** @var Purchase $purchasedProduct */
        $purchasedProduct = $this->purchaseTable->findWithProduct((int) $request->getAttribute('id'));
        $year = explode('-', $purchasedProduct->created_at)[0];
        $user = $this->auth->getUser();
        if ((int) $user->id !== (int) $purchasedProduct->user_id) {
            throw new ForbiddenException("Vous ne pouvez pas voir cette facture");
        }
        return $this->renderer->render('@shop/invoice', compact('purchasedProduct', 'user', 'year'));
    }
}
