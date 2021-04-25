<?php

namespace tests\Framework;

use App\Blog\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{

    private Router $router;

    public function setUp(): void
    {
        $this->router = new Router();
    }

    public function testGetMethod()
    {
        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blog', function () {
            return 'hello';
        }, 'blog');
        $route = $this->router->match($request);
        $this->assertEquals('blog', $route->getName());
        $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));
    }

    public function testGetMethodURLDoesntExists()
    {
        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blogaze', function () {
            return 'hello';
        }, 'blog');
        $route = $this->router->match($request);
        $this->assertEquals(null, $route);
    }

    public function testGetMethodWithParams()
    {
        $request = new ServerRequest('GET', '/blog/mon-slug-8');
        $this->router->get('/blog', function () {
            return 'azeaze';
        }, 'posts');
        $this->router->get('/blog/[*:slug]-[i:id]', function () {
            return 'hello';
        }, 'post.show');
        $route = $this->router->match($request);
        $this->assertEquals('post.show', $route->getName());
        $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));
        $this->assertEquals(['slug' => 'mon-slug', 'id' => '8'], $route->getParams());
    }

    public function testGenerateUri()
    {
        $this->router->get('/blog', function () {
            return 'azeaze';
        }, 'posts');
        $this->router->get('/blog/[*:slug]-[i:id]', function () {
            return 'hello';
        }, 'post.show');
        $uri = $this->router->setUri('post.show', ['slug' => 'mon-slug', 'id' => 8]);
        $this->assertEquals('/blog/mon-slug-8', $uri);
    }

    public function testGenerateUriWithQueryArgument()
    {
        $this->router->get('/blog', function () {
            return 'azeaze';
        }, 'posts');
        $this->router->get('/blog/[*:slug]-[i:id]', function () {
            return 'hello';
        }, 'post.show');
        $uri = $this->router->setUri('post.show', ['slug' => 'mon-slug', 'id' => 8], ['p' => 2]);
        $this->assertEquals('/blog/mon-slug-8?p=2', $uri);
    }
}
