<?php

namespace Framework\Database;

use Pagerfanta\Adapter\AdapterInterface;
use PDO;

class PaginatedQuery implements AdapterInterface
{
    private PDO $pdo;
    private string $query;
    private string $countQuery;
    private string $entity;

    /**
     * PaginatedQuery constructor.
     * @param PDO $pdo
     * @param string $query
     * @param string $countQuery
     * @param string|null $entity
     */
    public function __construct(PDO $pdo, string $query, string $countQuery, ?string $entity = null)
    {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->countQuery = $countQuery;
        $this->entity = $entity;
    }


    public function getNbResults(): int
    {
        return $this->pdo->query($this->countQuery)->fetchColumn();
    }

    public function getSlice(int $offset, int $length): iterable
    {
        $statement = $this->pdo->prepare($this->query . ' LIMIT :offset, :length');
        $statement->bindParam('offset', $offset, PDO::PARAM_INT);
        $statement->bindParam('length', $length, PDO::PARAM_INT);
        if (!is_null($this->entity)) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        $statement->execute();
        return $statement->fetchAll();
    }
}
