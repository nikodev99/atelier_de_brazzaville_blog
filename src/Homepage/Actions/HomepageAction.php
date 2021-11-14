<?php

namespace App\Homepage\Actions;

use App\Admin\Tables\MessageTable;
use App\Blog\Table\PostTable;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomepageAction
{


    private RendererInterface $renderer;

    private PostTable $table;
    private MessageTable $messageTable;

    public function __construct(RendererInterface $renderer, PostTable $table, MessageTable $messageTable)
    {
        $this->renderer = $renderer;
        $this->table = $table;
        $this->messageTable = $messageTable;
    }

    public function __invoke(ServerRequestInterface $request): string
    {
        return $this->index();
    }

    public function index(): string
    {
        $preteens = $this->table->findByCategory(4, 2);
        $children = $this->table->findByCategory(3);
        $jewelries = $this->table->findByCategory(1, 2);
        $bags = $this->table->findByCategory(2, 2);
        $clothing = $this->table->findByCategory(5, 2);
        $decoration = $this->table->findByCategory(6, 2);
        $newPosts = $this->table->findPostsByField("created_date");
        $famousPosts = $this->table->findPostsByField("view");
        $topPage = $this->table->findPostsByField(null, 5, true);
        $message = $this->messageTable->getMessage();
        return $this->renderer->render('@homepage/index', compact(
            'preteens',
            'children',
            'jewelries',
            'bags',
            'clothing',
            'decoration',
            'newPosts',
            'famousPosts',
            'topPage',
            'message'
        ));
    }
}
