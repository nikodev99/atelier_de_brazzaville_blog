<?php

namespace App\Blog\Table;

use App\Blog\Entity\Comment;
use App\Blog\Entity\Post;
use Framework\Database\Table;

class CommentTable extends Table
{
    protected string $table = 'comments';

    protected ?string $entity = Comment::class;

    public function getPostComments(Post $post): array
    {
        $request = "SELECT c.*, u.username FROM comments AS c JOIN users as u ON c.user_id = u.id JOIN posts p on c.post_id = p.id WHERE p.id = :id";
        $result = $this->getPdo()->prepare($request);
        $this->checkEntity($result);
        $result->execute(['id' => $post->id]);
        return $result->fetchAll();
    }

    protected function paginationQuery(bool $limit = false, int $dataLimit = 3): string
    {
        return "SELECT c.*, u.username, p.slug FROM comments AS c JOIN users as u ON c.user_id = u.id JOIN posts p on c.post_id = p.id ORDER BY created_at DESC LIMIT 10";
    }
}
