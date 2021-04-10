<?php

namespace Test\Framework\Twig;

use Framework\Twig\FormTwigExtension;
use PHPUnit\Framework\TestCase;

class FormTwigExtensionTest extends TestCase
{
    private FormTwigExtension $formExtension;

    protected function setUp(): void
    {
        $this->formExtension = new FormTwigExtension();
    }

    public function testInput()
    {
        $html = $this->formExtension->field([], 'title', 'Titre *', 'demo', ['placeholder' => 'titre']);
        $expected = "
        <div class=\"col-lg-6 col-md-6 col-sm-6 col-xs-12\">
            <div class=\"basic-login-inner\">
                <div class=\"form-group-inner\">
                    <label for=\"title\">Titre *</label>
                    <input type=\"text\" name=\"title\" class=\"form-control\" placeholder=\"titre\" id=\"title\" value=\"demo\" >
                </div>
            </div>
        </div>
        ";
        $this->assertSimilar($this->trim($expected), $this->trim($html));
    }

    public function testTextarea()
    {
        $html = $this->formExtension->field([], 'title', 'Titre *', 'demo', ['col' => 12, 'type' => 'textarea', 'placeholder' => 'titre']);
        $expected = "
        <div class=\"col-lg-12 col-md-12 col-sm-12 col-xs-12\">
            <div class=\"basic-login-inner\">
                <div class=\"form-group-inner\">
                    <label for=\"title\">Titre *</label>
                    <textarea rows=\"10\" class=\"form-control\" placeholder=\"titre\" name=\"title\"  id=\"title\" >demo</textarea>
                </div>
            </div>
        </div>
        ";
        $this->assertSimilar($this->trim($expected), $this->trim($html));
    }

    public function testInputWithError()
    {
        $context = ['errors' => ['title' => 'erreur']];
        $html = $this->formExtension->field($context, 'title', 'Titre *', 'demo', ['placeholder' => 'titre']);
        $expected = "
        <div class=\"col-lg-6 col-md-6 col-sm-6 col-xs-12\">
            <div class=\"basic-login-inner\">
                <div class=\"form-group-inner input-with-error\">
                    <label for=\"title\">Titre *</label>
                    <input type=\"text\" name=\"title\" class=\"form-control\" placeholder=\"titre\" id=\"title\" value=\"demo\" >
                    <small class=\"form-text text-muted\">erreur</small>
                </div>
            </div>
        </div>
        ";
        $this->assertSimilar($this->trim($expected), $this->trim($html));
    }

    private function assertSimilar($expected, $actual)
    {
        self::assertEquals($expected, $actual);
    }

    private function trim(string $string): string
    {
        $lines = array_map('trim', explode(PHP_EOL, $string));
        return implode('', $lines);
    }
}
