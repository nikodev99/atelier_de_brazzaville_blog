<?php

namespace App\Blog\Actions;

use App\Auth\Entity\User;
use App\Blog\Entity\Post;
use App\Blog\Table\CommentTable;
use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Auth;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CommentAction
{
    use RouterAwareAction;

    private RendererInterface $renderer;
    private CommentTable $commentTable;
    private Auth $auth;
    private PostTable $postTable;
    private Router $router;
    private FlashService $flash;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        Auth $auth,
        PostTable $postTable,
        CommentTable $commentTable,
        FlashService $flash
    ) {
        $this->renderer = $renderer;
        $this->commentTable = $commentTable;
        $this->auth = $auth;
        $this->postTable = $postTable;
        $this->router = $router;
        $this->flash = $flash;
    }

    /**
     * @throws NoRecordException
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $post = $this->postTable->find($request->getAttribute('id'));
        if ($this->validate($request)->isValid()) {
            $params = $request->getParsedBody();
            /** @var User $user */
            $user = $this->auth->getUser();
            /** @var Post $post */
            $this->commentTable->add([
                'user_id'   =>  $user->id,
                'post_id'   =>  $post->id,
                'comment'   =>  $params['comment'],
                'created_at'    =>  date('Y-m-d H:i:s')
            ]);
            $this->flash->success("Votre commentaire a été posté avec succès");
        } else {
            $this->flash->error("Erreur de soumission du commentaire");
        }
        return $this->redirect('blog.show', [
            'slug'  =>  $post->slug,
            'id'    =>  $post->id
        ]);
    }

    private function validate(ServerRequestInterface $request): Validator
    {
        return (new Validator($request->getParsedBody()))
            ->unEmptied('comment')
            ->required('comment');
    }
}
