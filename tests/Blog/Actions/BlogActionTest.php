<?php

namespace  Test\Blog\Actions;

use App\Blog\Actions\BlogAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class BlogActionTest extends TestCase
{
    private BlogAction $action;
    private $renderer;
    private $pdo;
    private $router;

    protected function setUp(): void
    {
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->pdo = $this->prophesize(\PDO::class);
        $this->router = $this->prophesize(Router::class);
        $this->action = new BlogAction($this->renderer->reveal(), $this->pdo->reveal(), $this->router->reveal());
    }

    public function testShowRedirect()
    {
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('slug', 'demo')
            ->withAttribute('id', '9');
        $response = $this->action->show($request);
        $this->assertEquals(301, $response->getStatusCode());
    }
}
