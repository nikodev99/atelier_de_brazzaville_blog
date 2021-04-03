<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Framework\Database\PaginatedQuery;
use Pagerfanta\Pagerfanta;
use PDO;

class PostTable
{
    private PDO $pdo;

    private const TABLE = 'posts';

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            'SELECT * FROM ' . self::TABLE . ' ORDER BY created_date DESC',
            'SELECT COUNT(id) FROM posts',
            Post::class
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findAll(): array
    {
        $query = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " ORDER BY created_date DESC");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS, Post::class);
        return $query->fetchAll();
    }

    public function find(int $id): ?Post
    {
        $query = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE id = ?");
        $query->execute([$id]);
        $query->setFetchMode(PDO::FETCH_CLASS, Post::class);
        $post = $query->fetch();
        if (is_bool($post)) {
            return null;
        }
        return $post;
    }

    public function add(array $params): int
    {
        $fieldQuery = $this->buildingFieldQuery($params, true);
        $fields = join(', ', array_keys($params));
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " ({$fields}) VALUES ({$fieldQuery})");
        $statement->execute($params);
        return $this->pdo->lastInsertId();
    }

    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildingFieldQuery($params);
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET $fieldQuery WHERE id = :id");
        return $statement->execute(array_merge($params, ['id' => $id]));
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM ' . self::TABLE . ' WHERE id = ?');
        return $statement->execute([$id]);
    }

    private function buildingFieldQuery(array $fieldsArray, bool $insert = false): string
    {
        if ($insert) {
            return join(', ', array_map(function ($field) {
                return ":$field";
            }, array_keys($fieldsArray)));
        }
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($fieldsArray)));
    }
}
