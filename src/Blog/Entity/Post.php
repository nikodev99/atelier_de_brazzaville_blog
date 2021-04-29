<?php

namespace App\Blog\Entity;

use DateTime;
use DateTimeZone;
use Error;
use Exception;

class Post
{
    public int $id;

    public string $title;

    public string $slug;

    public string $content;

    public $created_date;

    public $apdated_date;

    public int $view;

    public ?string $image;

    public string $category_name;

    private const PATH = '../../style/upload/';

    public function __construct()
    {
        $this->content = str_replace("<ul>", '<ul class="check">', $this->content);
        $this->content = str_replace("<blockquote>", '<ul class="blockquote">', $this->content);

        if ($this->created_date) {
            $this->created_date = $this->getDateTime($this->created_date);
        }
        if ($this->apdated_date) {
            $this->apdated_date = $this->getDateTime($this->apdated_date);
        }
    }

    public function getThumb(): ?string
    {
        return  self::PATH . $this->getPhoto('thumb', 'blog_square_01.jpg');
    }

    public function getMain(): ?string
    {
        return  self::PATH . $this->getPhoto('main', 'blog_10.jpg');
    }

    public function getLeft(): ?string
    {
        return  self::PATH . $this->getPhoto('left', 'blog_masonry_01.jpg');
    }

    public function getGreatMiddle(): ?string
    {
        return  self::PATH . $this->getPhoto('great_middle', 'blog_masonry_02.jpg');
    }

    public function getThumbMiddle(): ?string
    {
        return  self::PATH . $this->getPhoto('thumb_middle', 'blog_masonry_03.jpg');
    }

    public function getPrimary(): ?string
    {
        return  self::PATH . $this->getPhoto('primary', 'blog_05.jpg');
    }

    public function getMaternelle(): ?string
    {
        return  self::PATH . $this->getPhoto('maternelle', 'blog_01.jpg');
    }

    private function getPhoto(string $type, string $default): string
    {
        if (!is_null($this->image)) {
            ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);
            return $filename . "-$type.$extension";
        }
        return $default;
    }

    private function getDateTime($date): DateTime
    {
        try {
            return (new DateTime($date))->setTimezone(new DateTimeZone('Africa/Brazzaville'));
        } catch (Exception $e) {
            throw new Error("Type of date error: " . $e->getMessage());
        }
    }
}
