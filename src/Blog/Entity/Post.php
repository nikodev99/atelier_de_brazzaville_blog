<?php

namespace App\Blog\Entity;

use DateTime;
use DateTimeZone;

class Post
{

    public int $id;

    public string $title;

    public string $slug;

    public string $content;

    public $created_date;

    public $apdated_date;

    public int $view;

    public function __construct()
    {
        if ($this->created_date) {
            $this->created_date = $this->getDateTime($this->created_date);
        }
        if ($this->apdated_date) {
            $this->apdated_date = $this->getDateTime($this->apdated_date);
        }
    }

    private function getDateTime($date): DateTime
    {
        try {
            return (new DateTime($date))->setTimezone(new DateTimeZone('Africa/Brazzaville'));
        } catch (\Exception $e) {
            throw new \Error("Type of date error: " . $e->getMessage());
        }
    }
}
