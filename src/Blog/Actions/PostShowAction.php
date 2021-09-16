<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\Table\CommentTable;
use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface;

class PostShowAction
{
    use RouterAwareAction;

    private RendererInterface $renderer;

    /**
     * @var Router
     */
    private Router $router;

    /**
     * @var PostTable
     */
    private PostTable $postTable;
    private CommentTable $commentTable;

    public function __construct(RendererInterface $renderer, Router $router, PostTable $postTable, CommentTable $commentTable)
    {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postTable = $postTable;
        $this->commentTable = $commentTable;
    }

    /**
     * @throws NoRecordException
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $this->incrementView($request);
        $slug = $request->getAttribute('slug');
        /** @var Post $post */
        $post = $this->postTable->find((int) $request->getAttribute('id'));
        $comments = $this->commentTable->getPostComments($post);
        $commentsNumber = count($comments);
        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'slug'  =>  $post->slug,
                'id'    =>  $post->id
            ]);
        }
        $newPosts = $this->postTable->findPostsByField("created_date");
        $famousPosts = $this->postTable->findPostsByField("view");
        $likedPosts = $this->postTable->likedPost((int)$post->category_id, 2, (int)$post->id);
        return $this->renderer->render('@blog/show', [
            'post'  =>  $post,
            'comments'  =>  $comments,
            'number'    =>  $commentsNumber,
            'newPosts'  => $newPosts,
            'famousPosts'   =>  $famousPosts,
            'likedPosts'    =>  $likedPosts
        ]);
    }

    /**
     * @throws NoRecordException
     */
    private function incrementView(ServerRequestInterface $request): void
    {
        $p = $this->postTable->find((int) $request->getAttribute('id'));
        $viewIncrementation = (int)$p->view + 1;
        $this->postTable->update((int) $p->id, ['view' => $viewIncrementation]);
    }
}
