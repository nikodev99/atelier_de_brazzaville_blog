<?php

namespace Framework\Database;

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
        $pagerfanta = $this->getPagerfanta($query);
        return $pagerfanta
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

    /**
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value)
    {
        $statement = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE $field = ?");
        $statement->execute([$value]);
        if (isset($this->entity)) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        } else {
            $statement->setFetchMode(PDO::FETCH_OBJ);
        }
        $record = $statement->fetch();
        if (is_bool($record)) {
            throw new NoRecordException("L'extraction des données à échouer");
        }
        return $record;
    }

    /**
     * @throws NoRecordException
     */
    public function find(int $id)
    {
        $query = $this->pdo->prepare(
            "SELECT p.*, c.name as category_name, c.slug as category_slug FROM {$this->table} as p 
                    LEFT JOIN categories as c on p.category_id = c.id
                    WHERE p.id = ?"
        );
        $query->execute([$id]);
        if (isset($this->entity)) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        $post = $query->fetch();
        if (is_bool($post)) {
            throw new NoRecordException("L'extration des données à échouer");
        }
        return $post;
    }

    public function findByCategory(int $category_id, int $limit = 3): array
    {
        $query = $this->pdo->prepare($this->postCategoryQuery($limit));
        $query->execute([$category_id]);
        if (isset($this->entity)) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        return $query->fetchAll();
    }

    public function findPostsByField(string $field = null, int $limit = 3, bool $categories = false, int $id = 0): array
    {
        if ($categories) {
            $query = $this->pdo->prepare($this->paginationQuery(true, $limit));
        } else {
            $query = $this->pdo->prepare($this->byFieldQuery($field, $limit, $id));
        }
        $query->execute();
        if (isset($this->entity)) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        return $query->fetchAll();
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

    protected function getPagerfanta(PaginatedQuery $query): Pagerfanta
    {
        $pagerfanta = new Pagerfanta($query);
        $currentPage = isset($_GET['p']) ? (int) $_GET['p'] : 1;
        if ($pagerfanta->getNbPages() > 1 && $pagerfanta->getNbPages() < $currentPage) {
            die("<h1>La page $currentPage n'existe pas<h1>");
        }
        return $pagerfanta;
    }

    protected function paginationQuery(bool $limit = false, int $dataLimit = 3): string
    {
        return 'SELECT * FROM ' . $this->table;
    }

    protected function postCategoryQuery(int $limit): string
    {
        return 'SELECT * FROM ' . $this->table . ' WHERE category_id = ? ORDER BY created_date DESC LIMIT ' . $limit;
    }

    protected function byFieldQuery(string $field, int $limit, int $id = 0): string
    {
        $statement = 'SELECT * FROM ' . $this->table;
        if ($id !== 0) {
            $statement .= " WHERE id != $id ";
        }
        return $statement . ' ORDER BY ' . $field . ' DESC LIMIT ' . $limit;
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
