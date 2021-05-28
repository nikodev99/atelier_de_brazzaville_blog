<?php

namespace App\Admin;

use App\Blog\BlogWidget;
use App\Blog\Table\PostTable;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class DashboardAction
{
    private RendererInterface $renderer;
    private PostTable $table;
    private array $widgets;

    public function __construct(RendererInterface $renderer, PostTable $table, array $widgets)
    {
        $this->renderer = $renderer;
        $this->table = $table;
        $this->widgets = $widgets;
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
        return $this->renderer->render('@admin/dashboard', compact('items', 'widgets'));
    }
}
