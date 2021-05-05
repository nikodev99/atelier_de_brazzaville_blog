<?php

namespace App\Auth\Table;

use App\Auth\Entity\User;
use Framework\Database\NoRecordException;
use Framework\Database\Table;
use PDO;

class UserTable extends Table
{
    protected string $table = 'users';

    protected ?string $entity = User::class;

    /**
     * @throws NoRecordException
     */
    public function findUser(array $fields, string $matched): ?User
    {
        $conditions = [];
        $execution = [];
        foreach ($fields as $field) {
            $conditions[] = "$field = :$field";
            $execution[$field] = $matched;
        }
        $sqlStatement = "SELECT * FROM {$this->table} WHERE " . implode(' OR ', $conditions);
        $statement = $this->getPdo()->prepare($sqlStatement);
        $statement->execute($execution);
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

    public function find(int $id)
    {
        $query = $this->getPdo()->prepare(
            "SELECT * FROM {$this->table} WHERE id = ?"
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
}
