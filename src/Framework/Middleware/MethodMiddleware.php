<?php

namespace Framework\Middleware;

use Psr\Http\Message\ServerRequestInterface;

class MethodMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $parseBody = $request->getParsedBody();
        if (array_key_exists('_METHOD', $parseBody) && in_array($parseBody['_METHOD'], ['DELETE', 'PUT'])) {
            $request = $request->withMethod($parseBody['_METHOD']);
        }
        return $next($request);
    }
}
