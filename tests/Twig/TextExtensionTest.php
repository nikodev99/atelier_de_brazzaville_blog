<?php

namespace Test\Twig;

use Framework\Twig\TextTwigExtension;
use PHPUnit\Framework\TestCase;

class TextExtensionTest extends TestCase
{
    private TextTwigExtension $text;

    protected function setUp(): void
    {
        $this->text = new TextTwigExtension();
    }

    public function testExcerpt()
    {
        $content = "Salut";
        $this->assertEquals($content, $this->text->excerpt($content, 10));
    }

    public function testExcerptWithLongContent()
    {
        $content = "Salut les gens vous aller bien j'espère";
        $this->assertEquals("Salut les...", $this->text->excerpt($content, 10));
    }
}
