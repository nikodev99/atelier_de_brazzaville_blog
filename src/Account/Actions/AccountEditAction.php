<?php

namespace App\Account\Actions;

use App\Account\Entity\User;
use App\Auth\DatabaseAuth;
use App\Auth\PasswordHash;
use App\Auth\Table\UserTable;
use App\Blog\Actions\PostIndexAction;
use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Auth;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class AccountEditAction
{
    use RouterAwareAction;

    private RendererInterface $renderer;
    private Auth $auth;
    private PostTable $postTable;
    private FlashService $flash;
    private UserTable $userTable;
    private DatabaseAuth $databaseAuth;
    private Router $router;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        Auth $auth,
        PostTable $postTable,
        FlashService $flash,
        UserTable $userTable,
        DatabaseAuth $databaseAuth
    ) {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->postTable = $postTable;
        $this->flash = $flash;
        $this->userTable = $userTable;
        $this->databaseAuth = $databaseAuth;
        $this->router = $router;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        /** @var User|null $user */
        $user = $this->auth->getUser();
        $post = new PostIndexAction($this->renderer, $this->postTable);
        $famousPosts = $post->famous();
        $newPosts = $post->new();
        if ($request->getMethod() === 'POST') {
            $form_params = $request->getParsedBody();
            $uri = $request->getUri()->getQuery();
            $redirectUri = $this->redirect('account.edit');
            if (strpos($uri, 'info')) :
                $validator = new Validator($form_params);
                $validator
                    ->unEmptied('last_name', 'first_name', 'email', 'username')
                    ->required('last_name', 'first_name', 'email', 'username')
                    ->length('username', 4)
                    ->email('email')
                ;
                /*if (!empty($form_params['city'])) {

                }
                if (!empty($form_params['address'])) {

                }*/
                if ($form_params['username'] !== $user->username) {
                    $validator->unique('username', $this->userTable);
                }
                if ($form_params['email'] !== $user->email) {
                    $validator->unique('email', $this->userTable);
                }
                if ($validator->isValid()) {
                    if (array_key_exists('country', $form_params) && $form_params['country'] === 'AF') {
                        $form_params['country'] = null;
                    }
                    $this->userTable->update($user->id, $form_params);
                    $this->flash->success('Félicitation vos informations ont bien été mis à jour !');
                    return $redirectUri;
                } else {
                    $errors = $validator->getErrors();
                    $this->flash->error('Des erreurs ont été constaté. Veuillez bien remplir le formulaire');
                    return $this->edit(compact('user', 'famousPosts', 'newPosts', 'errors'));
                }
            elseif (strpos($uri, 'pass')) :
                $validator = (new Validator($form_params))
                    ->unEmptied('password', 'password_confirm')
                    ->required('password', 'password_confirm')
                    ->length('password', 6)
                    ->confirm('password')
                ;
                if ($validator->isValid()) {
                    unset($form_params['password_confirm']);
                    $form_params['password'] = PasswordHash::hash($form_params['password']);
                    $this->userTable->update($user->id, $form_params);
                    $this->flash->success('Félicitation votre mot de passe été mis à jour !');
                    return $redirectUri;
                } else {
                    $errors = $validator->getErrors();
                    $this->flash->error('Des erreurs ont été constaté. Veuillez bien remplir le formulaire');
                    return $this->edit(compact('user', 'famousPosts', 'newPosts', 'errors'));
                }
            elseif (strpos($uri, 'delete') !== false) :
                $this->databaseAuth->logout();
                $this->userTable->delete($user->id);
                $this->flash->success("Votre compte a été supprimé avec succès");
                return $this->redirect('homepage.index');
            endif;
        }
        return $this->edit(compact('user', 'famousPosts', 'newPosts'));
    }

    private function edit(array $params): string
    {
        return $this->renderer->render('@account/edit', $params);
    }
}
