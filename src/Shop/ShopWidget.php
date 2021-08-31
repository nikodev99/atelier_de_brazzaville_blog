<?php

namespace App\Shop;

use App\Admin\AdminWidgetInterface;
use App\Shop\Table\ProductsTable;
use App\Shop\Table\PurchaseTable;
use Framework\Renderer\RendererInterface;

class ShopWidget implements AdminWidgetInterface
{

    private RendererInterface $renderer;
    private ProductsTable $productsTable;
    private PurchaseTable $purchaseTable;

    public function __construct(RendererInterface $renderer, ProductsTable $productsTable, PurchaseTable $purchaseTable)
    {
        $this->renderer = $renderer;
        $this->productsTable = $productsTable;
        $this->purchaseTable = $purchaseTable;
    }

    public function render(): string
    {
        $count = $this->productsTable->count();
        $income = $this->purchaseTable->getMonthIncome();
        return $this->renderer->render('@shop/admin/widget', compact('count', 'income'));
    }

    public function renderMenu(): string
    {
        return $this->renderer->render('@shop/admin/menu');
    }
}
