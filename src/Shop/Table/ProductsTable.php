<?php

namespace App\Shop\Table;

use App\Shop\Entity\Product;
use Framework\Database\PaginatedQuery;
use Framework\Database\Table;
use Pagerfanta\Pagerfanta;

class ProductsTable extends Table
{
    protected ?string $entity = Product::class;

    protected string $table = "products";

    private string $statement;

    public function findPublic(): self
    {
        $this->statement = "SELECT * FROM products WHERE created_at < NOW() ORDER BY created_at DESC";
        return $this;
    }

    public function paginate(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->getPdo(),
            $this->statement,
            'SELECT COUNT(id) FROM products',
            $this->entity ?? null
        );
        $pagerfanta = $this->getPagerfanta($query);
        return $pagerfanta
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    protected function findQuery(): string
    {
        return "SELECT * FROM products WHERE id = ?";
    }
}
