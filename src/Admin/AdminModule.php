<?php

namespace App\Admin;

use App\Admin\Actions\DashboardAction;
use App\Admin\Actions\MessageAction;
use App\Admin\Actions\ProfilAction;
use App\Admin\Actions\SettingAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRenderer;
use Framework\Router;
use Framework\Twig\AdminTwigExtension;

class AdminModule extends Module
{
    public const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(RendererInterface $renderer, Router $router, string $prefix, AdminTwigExtension $adminTwigExtension)
    {
        $renderer->addPath('admin', __DIR__ . '/templates');
        $router->get("$prefix/dashboard", DashboardAction::class, 'admin.post.index');
        $router->get("/admin/message-d-accueil", MessageAction::class, 'admin.message');
        $router->post("/admin/message-d-accueil", MessageAction::class);
        $router->get("/admin/profile", ProfilAction::class, "admin.profil");
        $router->post("/admin/profile", ProfilAction::class);
        $router->get("/admin/settings", SettingAction::class, "admin.setting");
        $router->post("/admin/settings", SettingAction::class);
        if ($renderer instanceof TwigRenderer) {
            $renderer->getTwig()->addExtension($adminTwigExtension);
        }
    }
}
