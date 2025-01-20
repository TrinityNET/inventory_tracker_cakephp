<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\Locator\TableLocator;
use Cake\Datasource\ConnectionManager;

/**
 * SeedProducts command.
 */
class SeedProductsCommand extends Command
{
    /**
     * Get the command description.
     *
     * @return string
     */
    public static function getDescription(): string
    {
        return 'Add 10 sample products to the database.';
    }

    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/5/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return parent::buildOptionParser($parser)
            ->setDescription(static::getDescription());
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int|null|void The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $tableLocator = new TableLocator();
        $productsTable = $tableLocator->get('Products');

        $productsTable->deleteAll([]); // Reset table before seeding

        $connection = $productsTable->getConnection();
        $connection->execute("DELETE FROM sqlite_sequence WHERE name = 'products'"); // Reset auto-increment on 'id'

        $data = [
            ['name' => 'Product A', 'quantity' => 20, 'price' => 25.00], // Promo
            ['name' => 'Product B', 'quantity' => 5, 'price' => 15.50], // Promo
            ['name' => 'Product C', 'quantity' => 0, 'price' => 60.00],
            ['name' => 'Product D', 'quantity' => 50, 'price' => 40.00],
            ['name' => 'Product E', 'quantity' => 100, 'price' => 150.00],
            ['name' => 'Product F', 'quantity' => 10, 'price' => 45.00],
            ['name' => 'Product G', 'quantity' => 0, 'price' => 10.00],
            ['name' => 'Product H', 'quantity' => 25, 'price' => 75.00],
            ['name' => 'Product I', 'quantity' => 12, 'price' => 200.00],
            ['name' => 'Product J', 'quantity' => 8, 'price' => 99.99],
        ];

        foreach ($data as $item) {
            $product = $productsTable->newEntity($item);
            if ($productsTable->save($product)) {
                $io->out("Seeded: " . $product->name);
            } else {
                $io->err("Failed to seed: " . $item['name']);
            }
        }

        $io->success('Products seeded successfully.');
        return 0; // Success code
    }
}
