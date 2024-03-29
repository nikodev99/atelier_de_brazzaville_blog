<?php

namespace Test\Framework;

use App\Blog\Renderer\PHPRenderer;
use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase
{

    private PHPRenderer $renderer;

    public function setUp(): void
    {
        $this->renderer = new PHPRenderer();
        $this->renderer->addPath(__DIR__ . '/views');
    }

    public function testAddingTheRightPath()
    {
        $this->renderer->addPath('blog', __DIR__ . '/views');
        $content = $this->renderer->render('@blog/demo');
        $this->assertEquals('Hello World', $content);
    }

    public function testRenderingTheDefaultPath()
    {
        $content = $this->renderer->render('demo');
        $this->assertEquals('Hello World', $content);
    }

    public function testRenderWithParams()
    {
        $content = $this->renderer->render('demoParams', ['name' => 'Nikhe']);
        $this->assertEquals('Salut Nikhe', $content);
    }

    public function testGlobalParams()
    {
        $this->renderer->addGlobal('name', 'Nikhe');
        $content = $this->renderer->render('demoParams');
        $this->assertEquals('Salut Nikhe', $content);
    }
}
