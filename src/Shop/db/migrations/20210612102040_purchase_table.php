<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PurchaseTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $this->table('purchases')
            ->addColumn('user_id', 'integer')
            ->addForeignKey('user_id', 'users', 'id')
            ->addColumn('product_id', 'integer')
            ->addForeignKey('product_id', 'products', 'id')
            ->addColumn('price', 'float', ['precision' => 10, 'scale' => 2])
            ->addColumn('vat', 'float', ['precision' => 10, 'scale' => 2])
            ->addColumn('country', 'string')
            ->addColumn('created_at', 'datetime')
            ->addColumn('stripe_id', 'string')
            ->create();
    }
}
