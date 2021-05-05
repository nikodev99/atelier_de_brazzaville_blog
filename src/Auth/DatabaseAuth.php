<?php

namespace App\Auth;

use App\Auth\Entity\User;
use App\Auth\Table\UserTable;
use Framework\Auth;
use Framework\Database\NoRecordException;
use Framework\Session\SessionInterface;

class DatabaseAuth implements Auth
{
    private UserTable $userTable;

    private SessionInterface $session;

    /**
     * @var User
     */
    private $user;

    /**
     * DatabaseAuth constructor.
     * @param UserTable $userTable
     * @param SessionInterface $session
     */
    public function __construct(UserTable $userTable, SessionInterface $session)
    {
        $this->userTable = $userTable;
        $this->session = $session;
    }

    /**
     * @throws NoRecordException
     */
    public function login(string $username, string $password): ?User
    {
        if (empty($username) || empty($password)) {
            return null;
        }
        /** @var User|null $user */
        $user = $this->userTable->findUser(['username', 'email'], $username);
        if ($user && PasswordHash::verify($password, $user->password)) {
            $this->session->set('auth.user', $user->id);
            return $user;
        }
        return null;
    }

    public function getUser(): ?User
    {
        if ($this->user) {
            return $this->user;
        }
        $userId = $this->session->get('auth.user');
        if ($userId) {
            try {
                $this->user = $this->userTable->find($userId);
                return $this->user;
            } catch (NoRecordException $e) {
                $this->session->delete($userId);
                return null;
            }
        }
        return null;
    }

    public function getUsername()
    {
    }

    public function logout(): void
    {
        $this->session->delete('auth.user');
    }
}
