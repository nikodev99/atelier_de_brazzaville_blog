<?php

namespace App\Homepage\Actions;

use App\Blog\Table\PostTable;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomepageAction
{


    private RendererInterface $renderer;

    private PostTable $table;

    public function __construct(RendererInterface $renderer, PostTable $table)
    {
        $this->renderer = $renderer;
        $this->table = $table;
    }

    public function __invoke(ServerRequestInterface $request): string
    {
        return $this->index();
    }

    public function index(): string
    {
        $preteens = $this->table->findByCategory(4, 2);
        $children_retrieved = $this->table->findByCategory(3, 4);
        $children = [];
        foreach ($children_retrieved as $key => $child_retrieved) {
            if ($key < 2) {
                $children[0][] = [
                    'url'   =>  '/blog/' . $child_retrieved->slug . '-' . $child_retrieved->id,
                    'title' =>  $child_retrieved->title,
                    'date'  =>  $child_retrieved->created_date->format('d M Y H:i'),
                    'image'  =>  $child_retrieved->getMaternelle()
                ];
            } else {
                $children[1][] = [
                    'url'   =>  '/blog/' . $child_retrieved->slug . '-' . $child_retrieved->id,
                    'title' =>  $child_retrieved->title,
                    'date'  =>  $child_retrieved->created_date->format('d M Y H:i'),
                    'image'  =>  $child_retrieved->getMaternelle()
                ];
            }
        }
        $jewelries = $this->table->findByCategory(1);
        $bags = $this->table->findByCategory(2);
        $clothing = $this->table->findByCategory(5);
        $decoration = $this->table->findByCategory(6);
        $newPosts = $this->table->findPostsByField("created_date");
        $famousPosts = $this->table->findPostsByField("view");
        $topPage = $this->table->findPostsByField(null, 5, true);
        return $this->renderer->render('@homepage/index', compact(
            'preteens',
            'children',
            'jewelries',
            'bags',
            'clothing',
            'decoration',
            'newPosts',
            'famousPosts',
            'topPage'
        ));
    }
}
