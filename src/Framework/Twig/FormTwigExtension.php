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
        <div class=\"col-lg-$col col-md-$col col-sm-$col col-xs-12\">
            <div class=\"basic-login-inner\">
                <div class=\"$class\">
                    <label for=\"$key\">$label</label>
                    $input
                    $error
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
        $optionsToSet = $this->setOptions($options);
        $class = 'form-control ' . ($options['class'] ?? '');
        return "<input type=\"$type\" name=\"$key\" class=\"$class\" id=\"$key\" value=\"$value\" $optionsToSet>";
    }

    private function textarea(string $key, $value, array $options = []): string
    {
        $optionsToSet = $this->setOptions($options);
        return "<textarea rows=\"12\" class=\"form-control\" name=\"$key\"  id=\"$key\" $optionsToSet>$value</textarea>";
    }

    private function select(string $key, ?string $value, array $options = []): string
    {
        $select_options = [];
        $optionsToSet = $this->setOptions($options);
        if (!empty($options['options'])) {
            foreach ($options['options'] as $k => $option) {
                $selected = $k == $value ? 'selected' : '';
                $select_options[] = '<option value="' . $k . '" ' . $selected . '>' . $option . '</option>';
            }
            return "
            <select class=\"form-control custom-select-value\" name=\"$key\" id=\"$key\" $optionsToSet>
                " . implode(PHP_EOL, $select_options) . "
            </select>
            ";
        }
        return "<select class=\"form-control custom-select-value selectpicker countrypicker\" name=\"$key\" id=\"$key\" data-default='$value'></select>";
    }

    private function setOptions(array $options): string
    {
        $optionSetting = [];
        foreach ($options as $key => $option) {
            $specialOptions = ['options', 'type', 'col', 'class'];
            if (!in_array($key, $specialOptions)) {
                if (is_bool($option) && $option === true) {
                    $optionSetting[] = "$key";
                }
                $optionSetting[] = "$key=\"$option\" ";
            }
        }
        return join(' ', $optionSetting);
    }

    private function convertedValue($value): string
    {
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        return (string) $value;
    }
}
