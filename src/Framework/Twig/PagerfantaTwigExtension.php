<?php

namespace Framework\Twig;

use Framework\Router;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrap4View;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PagerfantaTwigExtension extends AbstractExtension
{
    private Router $router;

    /**
     * PagerfantaTwigExtension constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }


    public function getFunctions(): array
    {
        return [
            new TwigFunction('paginate', [$this, 'paginate'], ['is_safe' => ['html']])
        ];
    }

    public function paginate(Pagerfanta $pager, string $route, array $routerParams = [], array $queryArgs = []): string
    {
        $view = new TwitterBootstrap4View();
        return $view->render($pager, function (int $page) use ($route, $routerParams, $queryArgs) {
            if ($page > 1) {
                $queryArgs['p'] = $page;
            }
            return $this->router->setUri($route, $routerParams, $queryArgs);
        });
    }
}
