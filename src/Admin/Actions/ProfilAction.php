<?php

namespace App\Admin\Actions;

use App\Auth\Entity\User;
use App\Auth\PasswordHash;
use App\Auth\Table\UserTable;
use Framework\Auth;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class ProfilAction
{
    private RendererInterface $renderer;
    private UserTable $userTable;
    private Auth $user;
    private FlashService $flash;

    public function __construct(RendererInterface $renderer, Auth $user, UserTable $userTable, FlashService $flash)
    {
        $this->renderer = $renderer;
        $this->userTable = $userTable;
        $this->user = $user;
        $this->flash = $flash;
    }

    /**
     * @throws NoRecordException
     */
    public function __invoke(ServerRequestInterface $request): string
    {
        /** @var User $user */
        $user = $this->user->getUser();
        $admin = $this->userTable->find($user->id);
        if ($request->getMethod() === "POST") {
            $params = $request->getParsedBody();
            $paramsKeys = array_keys($params);
            foreach ($paramsKeys as $key) {
                if ($key === "password") {
                    $isValid = (new Validator($params))->confirm("password");
                    if ($isValid->isValid()) {
                        $password = PasswordHash::hash($params["password"]);
                        $params["password"] = $password;
                    } else {
                        $this->flash->error("error rencontrer");
                        return $this->renderer->render("@admin/profile", [
                            "admin" =>  $admin
                        ]);
                    }
                }
                $key !== "password_confirm" ? $this->userTable->update($user->id, [$key => $params[$key]]) : null;
            }
            $this->flash->success("Vos informations ont bien été mis à jour");
        }
        $admin = $this->userTable->find($user->id);
        return $this->renderer->render("@admin/profile", [
            "admin" =>  $admin
        ]);
    }

    private function validator(ServerRequestInterface $request, string $key): Validator
    {
        return (new Validator($request->getParsedBody()))
            ->confirm("password")
        ;
    }
}
