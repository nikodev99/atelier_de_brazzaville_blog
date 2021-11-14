<?php

namespace App\Admin\Actions;

use App\Admin\Tables\MessageTable;
use Framework\Renderer\RendererInterface;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;

class MessageAction
{
    private RendererInterface $renderer;
    private MessageTable $table;
    private FlashService $flash;

    public function __construct(RendererInterface $renderer, MessageTable $table, FlashService $flash)
    {

        $this->renderer = $renderer;
        $this->table = $table;
        $this->flash = $flash;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        if (strpos($request->getUri()->getPath(), "message")) {
            $exist = $this->table->getMessage();
            if ($request->getMethod() === "POST") {
                $content = $request->getParsedBody()['content'];
                if ($exist === null) {
                    $this->table->add([
                        "content"   =>  $content,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s')
                    ]);
                } else {
                    $this->table->update(1, [
                        "content"   =>  $content,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s')
                    ]);
                }
                $this->flash->success("Message d'accueil ajouter avec succès");
            }
            return $this->renderer->render("@admin/message", compact('exist'));
        }
    }
}
