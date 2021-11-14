<?php

namespace App\Admin\Actions;

use App\Admin\AdminWidgetInterface;
use App\Admin\Tables\MessageTable;
use App\Auth\Table\UserTable;
use App\Blog\Table\CommentTable;
use App\Blog\Table\PostTable;
use App\Shop\Table\ProductsTable;
use App\Shop\Table\PurchaseTable;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class DashboardAction
{
    private RendererInterface $renderer;
    private PostTable $table;
    private array $widgets;
    private PurchaseTable $purchaseTable;
    private ProductsTable $productsTable;
    private MessageTable $messageTable;
    private UserTable $userTable;
    private CommentTable $commentTable;

    public function __construct(
        RendererInterface $renderer,
        PostTable $table,
        UserTable $userTable,
        ProductsTable $productsTable,
        PurchaseTable $purchaseTable,
        CommentTable $commentTable,
        MessageTable $messageTable,
        array $widgets
    ) {
        $this->renderer = $renderer;
        $this->table = $table;
        $this->widgets = $widgets;
        $this->purchaseTable = $purchaseTable;
        $this->productsTable = $productsTable;
        $this->messageTable = $messageTable;
        $this->userTable = $userTable;
        $this->commentTable = $commentTable;
    }

    public function __invoke(ServerRequestInterface $request): string
    {
        return $this->index($request);
    }

    public function index(ServerRequestInterface $request): string
    {
        $params = $request->getQueryParams();
        $currentPage = $params['p'] ?? 1;
        $items = $this->table->findPaginated(6, $currentPage);
        $widgets = array_reduce($this->widgets, function (string $html, AdminWidgetInterface $widget) {
            return $html . $widget->render();
        }, '');
        $stockPrice = $this->productsTable->getStockValue();
        $weekIncome = $this->purchaseTable->getWeekIncome();
        $monthIncome = $this->purchaseTable->getMonthIncome();
        $yearIncome = $this->purchaseTable->getYearIncome();
        $message = $this->messageTable->getMessage();
        $userCount = $this->userTable->count("WHERE role != 'admin'");
        $comments = $this->commentTable->findAll();
        $purchases = $this->purchaseTable->findAll();
        return $this->renderer->render('@admin/dashboard', compact(
            'items',
            'widgets',
            'stockPrice',
            'weekIncome',
            'monthIncome',
            'yearIncome',
            'message',
            'userCount',
            'comments',
            'purchases'
        ));
    }
}
