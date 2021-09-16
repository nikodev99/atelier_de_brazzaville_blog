<?php

namespace App\Auth\Actions;

use App\Auth\DatabaseAuth;
use App\Blog\Table\CommentTable;
use Framework\Actions\RouterAwareAction;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginAttemptAction
{
    use RouterAwareAction;

    private RendererInterface $renderer;

    private DatabaseAuth $auth;

    private Router $router;

    private SessionInterface $session;
    private CommentTable $commentTable;

    /**
     * LoginAttemptAction constructor.
     * @param RendererInterface $renderer
     * @param DatabaseAuth $auth
     * @param Router $router
     * @param SessionInterface $session
     * @param CommentTable $commentTable
     */
    public function __construct(
        RendererInterface $renderer,
        DatabaseAuth $auth,
        Router $router,
        SessionInterface $session,
        CommentTable $commentTable
    ) {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->router = $router;
        $this->session = $session;
        $this->commentTable = $commentTable;
    }


    /**
     * @throws NoRecordException
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $user = $this->auth->login($params['username'], $params['password']);
        if ($user) {
            $path = $this->session->get('auth.redirect') ?: $this->router->setUri('admin.post.index');
            $this->session->delete('auth.redirect');
            if ($this->session->get('comments')) {
                $comments = $this->session->get('comments');
                $this->commentTable->add([
                    'user_id'   =>  $user->id,
                    'post_id'   =>  $comments['post'],
                    'comment'   =>  $comments['comment'],
                    'created_at'    =>  $comments['create']
                ]);
            }
            $this->session->delete('comments');
            return new RedirectResponse($path);
        } else {
            (new FlashService($this->session))->error('Identifiant ou mot de passe incorrect');
            return $this->renderer->render('@auth/login', []);
        }
    }
}
