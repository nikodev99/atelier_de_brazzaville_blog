<?php

namespace App\Blog\Table;

class PostTable
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findPaginated(): array
    {
        return $this->pdo
            ->query("SELECT * FROM posts ORDER BY created_date DESC LIMIT 6")
            ->fetchAll();
    }

    public function find(int $id): \stdClass
    {
        $query = $this->pdo->prepare("SELECT * FROM posts WHERE id = ?");
        $query->execute([$id]);
        return $query->fetch();
    }
}
