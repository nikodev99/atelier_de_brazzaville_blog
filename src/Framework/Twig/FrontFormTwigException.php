<?php

namespace Framework\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FrontFormTwigException extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('ff', [$this, 'field'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ])
        ];
    }

    public function field(array $context, string $key, string $label, $value = null, string $type = null): string
    {
        $type = is_null($type) ? 'text' : $type;
        $error = $this->getErrorHTML($context, $key);
        $class = 'inputBx';
        if (!empty($error)) {
            $class .= ' input-with-error';
        }
        $input = $this->input($type, $key, $value);
        return "
        <div class='$class'>
            <label for='$key'>$label</label>
            $input
            $error
        </div>
        ";
    }

    private function getErrorHTML(array $context, string $key): string
    {
        $error = $context['errors'][$key] ?? false;
        if ($error) {
            return '<small>' . $error . '</small>';
        }
        return '';
    }

    private function input(string $type, string $key, $value = null): string
    {
        if (!is_null($value)) {
            $value = " value='$value'";
        }
        return "<input type='$type' name='$key' id='$key'$value>";
    }
}
