<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Framework\Database\Table;

class PostTable extends Table
{

    protected ?string $entity = Post::class;

    protected string $table = "posts";

    protected function paginationQuery(): string
    {
        $fields = implode(', ', [
            'p.id', 'p.title', 'p.slug', 'p.content', 'p.created_date', 'p.apdated_date', 'p.view', 'c.name'
        ]);
        return "SELECT {$fields} FROM {$this->table} AS p" .
            " LEFT JOIN categories AS c ON p.category_id = c.id" .
            " ORDER BY created_date DESC";
    }
}
