<?php

namespace Framework\Database;

use App\Blog\Entity\Post;
use Pagerfanta\Pagerfanta;
use PDO;

class Table
{
    private PDO $pdo;

    protected string $table;

    protected ?string $entity;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            $this->paginationQuery(),
            'SELECT COUNT(id) FROM posts',
            $this->entity ?? null
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findAll(): array
    {
        $query = $this->pdo->prepare($this->paginationQuery());
        $query->execute();
        if (isset($this->entity)) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        return $query->fetchAll();
    }

    public function find(int $id)
    {
        $query = $this->pdo->prepare("SELECT * FROM " . $this->table . " WHERE id = ?");
        $query->execute([$id]);
        if (isset($this->entity)) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
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
        $statement = $this->pdo->prepare("INSERT INTO {$this->table} ({$fields}) VALUES ({$fieldQuery})");
        $statement->execute($params);
        return $this->pdo->lastInsertId();
    }

    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildingFieldQuery($params);
        $statement = $this->pdo->prepare("UPDATE " . $this->table . " SET $fieldQuery WHERE id = :id");
        return $statement->execute(array_merge($params, ['id' => $id]));
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM ' . $this->table . ' WHERE id = ?');
        return $statement->execute([$id]);
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function findList(): array
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM {$this->table}")
            ->fetchAll(PDO::FETCH_NUM);
        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }
        return $list;
    }

    public function exists(string $id): bool
    {
        $statement = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $statement->execute([$id]);
        return $statement->fetchColumn() !== false;
    }

    protected function paginationQuery(): string
    {
        return 'SELECT * FROM ' . $this->table;
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
