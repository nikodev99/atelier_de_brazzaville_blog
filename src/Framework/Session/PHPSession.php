<?php

namespace Framework\Session;

class PHPSession implements SessionInterface
{
    public function get(string $key, $default = null)
    {
        $this->sessionStarter();
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
        return $default;
    }

    public function set(string $key, $value): void
    {
        $this->sessionStarter();
        $_SESSION[$key] = $value;
    }

    public function delete(string $key): void
    {
        $this->sessionStarter();
        unset($_SESSION[$key]);
    }

    private function sessionStarter(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
