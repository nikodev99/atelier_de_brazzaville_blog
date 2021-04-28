<?php

namespace Framework\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LayoutTwigExtension extends AbstractExtension
{
    private TextTwigExtension $text;

    public function __construct(TextTwigExtension $text)
    {
        $this->text = $text;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('layout', [$this, 'layout'], [
                'is_safe' => ['html']
            ])
        ];
    }

    public function layout(array $data): string
    {
        $layout = [];
        foreach ($data as $d) {
            $layout[] = $this->allLayout($d);
        }
        return implode(PHP_EOL, $layout);
    }

    private function allLayout(array $data): string
    {
        $layout = [];
        foreach ($data as $d) {
            $layout[] = $this->singleLayout($d);
        }
        return '
         <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
         ' . implode(PHP_EOL, $layout) . '
        </div>
        ';
    }

    private function singleLayout(array $data): string
    {
        return '
        <div class="blog-box">
            <div class="post-media">
                <a href="' . $data['url'] . '" title="">
                    <img src="' . $data['image'] . '" alt="" class="img-fluid">
                    <div class="hovereffect">
                        <span></span>
                    </div><!-- end hover -->
                </a>
            </div><!-- end media -->
            <div class="blog-meta">
                <h4><a href="' . $data['url'] . '" title="' . $data['title'] . '">' . $this->text->excerpt($data['title'], 25) . '</a></h4>
                <small><a href="/enfants" title="">Enfants</a></small>
                <small><a href="blog-category-01.html" title="">' . $data['date'] . '</a></small>
            </div><!-- end meta -->
        </div><!-- end blog-box -->

        <hr class="invis">
        ';
    }
}
