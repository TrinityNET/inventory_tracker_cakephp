<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\ProductsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\ProductsController Test Case
 *
 * @uses \App\Controller\ProductsController
 */
class ProductsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Products',
    ];

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\ProductsController::index()
     */
    public function testIndex(): void
    {
        $this->get('/products');
        $this->assertResponseOk();
        $this->assertResponseContains('Products');
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\ProductsController::view()
     */
    public function testView(): void
    {
        $this->get('/products/view/1'); // Use id 1 to test view (won't show without there being a product)
        $this->assertResponseOk();
        $this->assertResponseContains('Products');
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\ProductsController::add()
     */
    public function testAdd(): void
    {
        $data = [
            'name' => 'Test Product',
            'quantity' => 10,
            'price' => 50.00,
        ];

        // Simulate CSRF and security tokens (fixed unit test fail)
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $this->post('/products/add', $data);
        $this->assertResponseSuccess();

        $products = $this->getTableLocator()->get('Products');
        $query = $products->find()->where(['name' => 'Test Product']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\ProductsController::edit()
     */
    public function testEdit(): void
    {
        $products = $this->getTableLocator()->get('Products');
        $product = $products->find()->first();

        // Simulate CSRF and security tokens (fixed unit test fail)
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $this->post('/products/edit/' . $product->id, [
            'name' => 'Updated Product',
            'quantity' => $product->quantity,
            'price' => $product->price,
        ]);
        $this->assertResponseSuccess();

        $updatedProduct = $products->get($product->id);
        $this->assertEquals('Updated Product', $updatedProduct->name);
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\ProductsController::delete()
     */
    public function testDelete(): void
    {
        $products = $this->getTableLocator()->get('Products');

        // Fetch an existing product
        $product = $products->find()->first();

        // Simulate CSRF and security tokens (fixed unit test fail)
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        // Simulate a POST request to the delete action
        $this->post('/products/delete/' . $product->id);

        // Assert the response was successful
        $this->assertResponseSuccess();

        // Fetch the product again to verify it was soft-deleted
        $deletedProduct = $products->get($product->id);
        $this->assertTrue($deletedProduct->deleted);
    }
}
