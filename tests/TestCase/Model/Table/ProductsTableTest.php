<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ProductsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ProductsTable Test Case
 */
class ProductsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ProductsTable
     */
    protected $Products;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Products',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Products') ? [] : ['className' => ProductsTable::class];
        $this->Products = $this->getTableLocator()->get('Products', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Products);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ProductsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $product = $this->Products->newEmptyEntity();
        $product = $this->Products->patchEntity($product, [
            'name' => 'Valid Product',
            'quantity' => 10,
            'price' => 100.00,
        ]);
        $this->assertEmpty($product->getErrors());
    }

    public function testValidationFails(): void
    {
        $product = $this->Products->newEmptyEntity();
        $product = $this->Products->patchEntity($product, [
            'name' => '', // Invalid name
            'quantity' => -1, // Invalid quantity
            'price' => -10.00, // Invalid price
        ]);

        $this->assertNotEmpty($product->getErrors());
        $this->assertArrayHasKey('name', $product->getErrors());
        $this->assertArrayHasKey('quantity', $product->getErrors());
        $this->assertArrayHasKey('price', $product->getErrors());
    }

    public function testBeforeSaveStatusCalculation(): void
    {
        $product = $this->Products->newEntity([
            'name' => 'New Product',
            'quantity' => 1, // Should trigger 'low stock'
            'price' => 100.00,
        ]);

        $this->Products->save($product);
        $this->assertEquals('low stock', $product->status);
    }
}
