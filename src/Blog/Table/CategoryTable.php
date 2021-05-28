<?php

namespace App\Blog\Table;

use Framework\Database\Table;
use stdClass;

class CategoryTable extends Table
{
    protected string $table = "categories";

    protected ?string $entity = stdClass::class;

    protected function findQuery(): string
    {
        return "SELECT * FROM {$this->table} WHERE id = ?";
    }
}
