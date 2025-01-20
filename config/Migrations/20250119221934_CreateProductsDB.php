<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateProductsDB extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        // Create products table in SQLite database.
        $table = $this->table('products');
        $table
            ->addColumn('name', 'string', [
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('quantity', 'integer', [
                'default' => 0,
                'null' => false,
            ])
            ->addColumn('price', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'null' => false,
            ])
            ->addColumn('status', 'string', [
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('last_updated', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP',
                'null' => false,
            ])
            ->addColumn('deleted', 'boolean', [ // Soft delete bool
                'default' => false,
                'null' => false,
            ])
            ->addIndex(['name'], ['unique' => true, 'name' => 'UNIQUE_NAME']) // Add unique constraint for validateUnique rule
            ->create();
    }
}
