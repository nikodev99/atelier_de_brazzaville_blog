<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Framework\Database\PaginatedQuery;
use Framework\Database\Table;
use Pagerfanta\Pagerfanta;

class PostTable extends Table
{

    protected ?string $entity = Post::class;

    protected string $table = "posts";

    public function findPaginatedPublic(int $perPage, int $currentPage, $category_id): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->getPdo(),
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM posts AS p 
                    LEFT JOIN categories c on c.id = p.category_id 
                    WHERE p.category_id = :category_id
                    ORDER BY created_date DESC",
            'SELECT COUNT(id) FROM posts WHERE category_id = :category_id',
            $this->entity ?? null,
            ['category_id' => $category_id]
        );
        return $this->getPagerfanta($query)
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findPaginatedByField(int $perPage, int $currentPage, string $field): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->getPdo(),
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM posts p 
                    LEFT JOIN categories c on c.id = p.category_id 
                    ORDER BY p.$field DESC",
            'SELECT COUNT(p.id) FROM posts p LEFT JOIN categories c on c.id = p.category_id',
            $this->entity ?? null,
        );
        return $this->getPagerfanta($query)
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function likedPost(int $category_id, int $limit, int $id = 0): array
    {
        $query = "SELECT * FROM posts WHERE id != :id AND category_id = :category ORDER BY view DESC LIMIT $limit";
        $result = $this->getPdo()->prepare($query);
        $this->checkEntity($result);
        $result->execute([
           "id" =>  $id,
           "category"   =>  $category_id
        ]);
        return $result->fetchAll();
    }

    protected function paginationQuery(bool $limit = false, int $dataLimit = 3): string
    {
        $statementWithLimit = '';
        if ($limit) {
            $statementWithLimit = ' LIMIT ' . $dataLimit;
        }
        return "SELECT p.*, c.name, c.slug as category_slug FROM $this->table AS p" .
            " LEFT JOIN categories AS c ON p.category_id = c.id" .
            " ORDER BY created_date DESC" . $statementWithLimit;
    }
}
