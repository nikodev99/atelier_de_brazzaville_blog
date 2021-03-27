<?php

namespace App\Framework\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Add the methods helpers when using a Router.
 *
 * Trait RouterAwareAction
 * @package App\Framework\Actions
 */
trait RouterAwareAction
{

    /**
     * Helper for redirection.
     *
     * @param string $path
     * @param array $params
     * @return ResponseInterface
     */
    public function redirect(string $path, array $params = []): ResponseInterface
    {
        $redirectUri = $this->router->setUri($path, $params);
        return (new Response())
            ->withStatus(301)
            ->withHeader('Location', $redirectUri);
    }
}
