<?php

namespace Framework\Twig;

use App\Admin\AdminWidgetInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminTwigExtension extends AbstractExtension
{
    private array $widgets;

    public function __construct(array $widgets)
    {
        $this->widgets = $widgets;
    }

    public function getFunctions(): array
    {
        return [
          new TwigFunction('admin_menu', [$this, 'menu'], ['is_safe' => ['html']])
        ];
    }

    public function menu(): string
    {
        return array_reduce($this->widgets, function (string $html, AdminWidgetInterface $widget) {
            return $html . $widget->renderMenu();
        }, '');
    }
}
