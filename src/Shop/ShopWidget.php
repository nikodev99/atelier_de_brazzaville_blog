<?php

namespace App\Shop;

use App\Admin\AdminWidgetInterface;
use App\Shop\Table\ProductsTable;
use Framework\Renderer\RendererInterface;

class ShopWidget implements AdminWidgetInterface
{

    private RendererInterface $renderer;
    private ProductsTable $productsTable;

    public function __construct(RendererInterface $renderer, ProductsTable $productsTable)
    {
        $this->renderer = $renderer;
        $this->productsTable = $productsTable;
    }

    public function render(): string
    {
        $count = $this->productsTable->count();
        return $this->renderer->render('@shop/admin/widget', compact('count'));
    }

    public function renderMenu(): string
    {
        return $this->renderer->render('@shop/admin/menu');
    }
}
