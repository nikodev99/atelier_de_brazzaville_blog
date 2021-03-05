<?php

namespace tests\Framework;

use Framework\App;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase {

    public function testRedirectTrailingSlash() {
        $app = new App();
        $request = new ServerRequest('GET', '/demoslash/');
        $response = $app->run($request);
        $this->assertContains('/demoslash', $response->getHeader('Location'));
        $this->assertEquals(301, $response->getStatusCode());
    }

    public function testPageContent() {
        $app = new App();
        $request = new ServerRequest('GET', '/blog');
        $response = $app->run($request);
        $this->assertStringContainsString('<h1>Welcome on the atelier of brazzaville blog</h1>', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testError404() {
        $app = new App();
        $request = new ServerRequest('GET', '/azeaze');
        $response = $app->run($request);
        $this->assertStringContainsString('<h1>Error 404 page not found</h1>', $response->getBody());
        $this->assertEquals(404, $response->getStatusCode());
    }

}