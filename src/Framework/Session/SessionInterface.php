<?php

namespace Framework\Session;

interface SessionInterface
{
    /**
     * Retrieves a value of the specify session key.
     * @param string $key
     * @param ?mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Set a session key and persistes a value in a session.
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void;

    /**
     * @param string $key
     * @return void
     */
    public function delete(string $key): void;
}
