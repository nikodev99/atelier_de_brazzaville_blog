<?php

namespace App\Blog\Entity;

use DateTime;
use DateTimeZone;
use Exception;
use Error;

class Comment
{
    private int $id = 0;
    private int $user_id = 0;
    private int $post_id = 0;
    private string $comment = '';

    /** @var DateTime  */
    private $created_at;

    public function __construct()
    {
        if ($this->created_at) {
            $this->created_at = $this->getDateTime($this->created_at);
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @return int
     */
    public function getPostId(): int
    {
        return $this->post_id;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
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
