<?php

namespace Framework\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TextTwigExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', [$this, 'excerpt'])
        ];
    }

    public function excerpt(?string $content, int $maxLength = 200): string
    {
        if (is_null($content)) {
            return '';
        } else {
            if (mb_strlen($content) > $maxLength) {
                $excerpt = mb_substr($content, 0, $maxLength);
                $lastSpace = mb_strrpos($excerpt, ' ');
                $content = mb_substr($excerpt, 0, $lastSpace) . '...';
            }
        }
        return $content;
    }
}
