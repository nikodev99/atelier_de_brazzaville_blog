<?php

namespace Framework\Auth;

use Framework\Auth;
use Framework\Session\PHPSession;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoggedInMiddleware implements MiddlewareInterface
{
    private Auth $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }


    /**
     * @throws ForbiddenException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (array_key_exists('comment', $request->getParsedBody())) {
            (new PHPSession())->set('comments', [
                'comment'   =>  $request->getParsedBody()['comment'],
                'post'      =>  $request->getAttribute('id'),
                'create'    =>  date('Y-m-d H:i:s')
            ]);
        }
        $user = $this->auth->getUser();
        if (is_null($user)) {
            throw new ForbiddenException();
        }
        return $handler->handle($request->withAttribute('user', $user));
    }
}
