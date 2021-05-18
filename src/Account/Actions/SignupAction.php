<?php

namespace App\Account\Actions;

use App\Auth\DatabaseAuth;
use App\Auth\Entity\User;
use App\Auth\PasswordHash;
use App\Auth\Table\UserTable;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class SignupAction
{
    private RendererInterface $renderer;

    private UserTable $table;

    private Router $router;

    private FlashService $flash;

    private DatabaseAuth $auth;

    public function __construct(
        RendererInterface $renderer,
        UserTable $table,
        Router $router,
        FlashService $flash,
        DatabaseAuth $auth
    ) {
        $this->renderer = $renderer;
        $this->table = $table;
        $this->router = $router;
        $this->flash = $flash;
        $this->auth = $auth;
    }

    /**
     * @throws NoRecordException
     */
    public function __invoke(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'POST') {
            $params = $request->getParsedBody();
            $validator = (new Validator($params))
                ->unEmptied('last_name', 'first_name', 'email', 'username', 'password', 'password_confirm')
                ->required('last_name', 'first_name', 'email', 'username', 'password', 'password_confirm')
                ->length('username', 4)
                ->email('email')
                ->confirm('password')
                ->unique('username', $this->table)
                ->unique('email', $this->table)
            ;
            if ($validator->isValid()) {
                unset($params['password_confirm']);
                $params['password'] = PasswordHash::hash($params['password']);
                $this->table->add($params);
                /** @var User $user */
                $user = $this->table->find((int)$this->table->getPdo()->lastInsertId());
                $slug = $user->username;
                $this->auth->setUser($user);
                $this->flash->success("Vous vous êtes inscris à latelierbrazzaville avec succès");
                return new RedirectResponse($this->router->setUri('account.profile', ['slug' => $slug]));
            } else {
                $errors = $validator->getErrors();
                $this->flash->error('Enregistrement impossible veuillez revérifier les informations fournis');
                return $this->renderer->render('@account/signup', compact('errors', 'params'));
            }
        }
        return $this->renderer->render('@account/signup');
    }
}
