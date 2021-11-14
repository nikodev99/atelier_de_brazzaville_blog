<?php

namespace App\Admin\Actions;

use App\Admin\Tables\SettingTable;
use Framework\Renderer\RendererInterface;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;

class SettingAction
{
    private RendererInterface $renderer;
    private SettingTable $settingTable;
    private FlashService $flash;

    public function __construct(RendererInterface $renderer, SettingTable $settingTable, FlashService $flash)
    {
        $this->renderer = $renderer;
        $this->settingTable = $settingTable;
        $this->flash = $flash;
    }

    public function __invoke(ServerRequestInterface $request): string
    {
        if ($request->getMethod() === "POST") {
            $params = $request->getParsedBody();
            $this->settingTable->updateKey("online", $params['online']);
            $this->flash->success("Valeur mis à jour");
        }
        $online = $this->settingTable->getKeyValue("online");
        return $this->renderer->render("@admin/setting", compact('online'));
    }
}