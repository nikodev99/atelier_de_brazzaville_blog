<?php

namespace Framework\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FormTwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('field', [$this, 'field'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ])
        ];
    }

    public function field(array $context, string $key, string $label, $value = null, array $options = []): string
    {
        $type = $options['type'] ?? 'text';
        $col = $options['col'] ?? 6;
        $error = $this->getErrorHTML($context, $key);
        $class = 'form-group-inner';
        $value = $this->convertedValue($value);
        if (!empty($error)) {
            $class .= ' input-with-error';
        }
        if ($type === 'textarea') {
            $input = $this->textarea($key, $value, $options);
        } elseif ($type === 'select') {
            $input = $this->select($key, $value, $options);
        } else {
            $input = $this->input($type, $key, $value, $options);
        }
        return "
        <div class=\"col-lg-{$col} col-md-{$col} col-sm-{$col} col-xs-12\">
            <div class=\"basic-login-inner\">
                <div class=\"{$class}\">
                    <label for=\"{$key}\">{$label}</label>
                    {$input}
                    {$error}
                </div>
            </div>
        </div>
        ";
    }

    private function getErrorHTML(array $context, string $key): string
    {
        $error = $context['errors'][$key] ?? false;
        if ($error) {
            return '<small class="form-text text-muted">' . $error . '</small>';
        }
        return '';
    }

    private function input(string $type, string $key, $value, array $options = []): string
    {
        $placeholder = $this->setOptions('placeholder', $options);
        $required = $this->setOptions('required', $options);
        $disabled = $this->setOptions('disabled', $options);
        $class = 'form-control ' . ($options['class'] ?? '');
        return "<input type=\"{$type}\" name=\"{$key}\" class=\"{$class}\"{$placeholder}id=\"{$key}\" value=\"{$value}\" {$required}{$disabled}>";
    }

    private function textarea(string $key, $value, array $options = []): string
    {
        $placeholder = $this->setOptions('placeholder', $options);
        $required = $this->setOptions('required', $options);
        $disabled = $this->setOptions('disabled', $options);
        return "<textarea rows=\"12\" class=\"form-control\"{$placeholder}name=\"{$key}\"  id=\"{$key}\" {$required}{$disabled}>{$value}</textarea>";
    }

    private function select(string $key, ?string $value, array $options = []): string
    {
        $select_options = [];
        if (!empty($options['options'])) {
            foreach ($options['options'] as $k => $option) {
                $selected = $k == $value ? 'selected' : '';
                $select_options[] = '<option value="' . $k . '" ' . $selected . '>' . $option . '</option>';
            }
            $required = $this->setOptions('required', $options);
            $disabled = $this->setOptions('disabled', $options);
            return "
            <select class=\"form-control custom-select-value\" name=\"{$key}\" id=\"{$key}\" {$required}{$disabled}>
                " . implode(PHP_EOL, $select_options) . "
            </select>
            ";
        }
        return "<select class=\"form-control custom-select-value selectpicker countrypicker\" name=\"{$key}\" id=\"{$key}\" data-default='$value'></select>";
    }

    private function setOptions(string $key, array $options, $expected = null): string
    {
        if (array_key_exists($key, $options) && is_bool($options[$key]) && $options[$key] === true) {
            return " $key";
        }
        if (array_key_exists($key, $options)) {
            return " $key=\"{$options[$key]}\" ";
        }
        return !is_null($expected) ? $expected : '';
    }

    private function convertedValue($value): string
    {
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        return (string) $value;
    }
}
