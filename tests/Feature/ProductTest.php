<?php

namespace Tests\Feature;

use App\Product;
use App\Voucher;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProductTest extends TestCase
{
    use DatabaseMigrations;

    public function testIsAvailable()
    {
        $response = $this->get('/api/products');
        $response->assertSuccessful();
    }

    public function testStructure()
    {
        factory(Product::class, 10)->create();
        $response = $this->get('/api/products');
        $response->assertJsonStructure([
            'count',
            'items' => ['*' => ['id', 'name', 'price', 'discount']]
        ]);
    }

    public function testSortingByName()
    {
        factory(Product::class)->create([
            'name' => 'Apple',
        ]);

        factory(Product::class)->create([
            'name' => 'Orange',
        ]);

        factory(Product::class)->create([
            'name' => 'Cucumber',
        ]);

        $response = $this->get('/api/products?sort=-name');
        $response->assertJson([
            'count' => 3,
            'items' => [
                ['name' => 'Orange'],
                ['name' => 'Cucumber'],
                ['name' => 'Apple'],
            ]
        ]);

        $response = $this->get('/api/products?sort=+name');
        $response->assertJson([
            'count' => 3,
            'items' => [
                ['name' => 'Apple'],
                ['name' => 'Cucumber'],
                ['name' => 'Orange'],
            ]
        ]);
    }


    public function testDiscount()
    {
        $product = factory(Product::class)->create([
            'name' => 'Apple',
        ]);

        $product->vouchers()->attach(factory(Voucher::class)->create([
            'discount' => 10,
        ]));

        $product->vouchers()->attach(factory(Voucher::class)->create([
            'discount' => 25,
        ]));

        $product->vouchers()->attach(factory(Voucher::class)->create([
            'discount' => 20,
            'start_date' => '2010-12-01',
            'end_date' => '2010-12-02',
        ]));

        $response = $this->get('/api/products');
        $response->assertJson([
            'count' => 1,
            'items' => [
                ['name' => 'Apple', 'discount' => 35],
            ]
        ]);
    }

    public function testDiscountLimit()
    {
        factory(Product::class, 2)->create()->each(function ($product) {
            $product->vouchers()->attach(factory(Voucher::class, 5)->create([
                'discount' => 25,
            ]));
        });

        $response = $this->get('/api/products');
        $products = $response->decodeResponseJson()['items'];
        foreach ($products as $product) {
            $this->assertEquals(60, $product['discount']);
        }
    }

    public function testBuyNotExist()
    {
        $response = $this->post('/api/products/1/buy', []);
        $response->assertStatus(404);
    }

    public function testBuy()
    {
        $product = factory(Product::class)->create([
            'name' => 'Apple',
        ]);

        $response = $this->post("/api/products/{$product->id}/buy", []);
        $response->assertStatus(200);
    }

    public function testBuyTwice()
    {
        $product = factory(Product::class)->create([
            'name' => 'Apple',
        ]);

        $response = $this->post("/api/products/{$product->id}/buy", []);
        $response->assertStatus(200);

        $response = $this->post("/api/products/{$product->id}/buy", []);
        $response->assertStatus(404);
    }

    public function testDisabledVoucherAfterPurchase()
    {
        $voucher = factory(Voucher::class)->create([
            'discount' => 25,
        ]);

        $product1 = factory(Product::class)
            ->create(['name' => 'Apple']);
        $product1->vouchers()
            ->attach($voucher);

        $product2 = factory(Product::class)
            ->create(['name' => 'Orange']);

        $product2->vouchers()
            ->attach($voucher);

        $response = $this->get('/api/products');
        $products = $response->decodeResponseJson()['items'];

        $this->assertCount(2, $products);
        foreach ($products as $product) {
            $this->assertEquals(25, $product['discount']);
        }

        $this->post("/api/products/{$product1->id}/buy", []);
        $response = $this->get('/api/products');
        $products = $response->decodeResponseJson()['items'];
        $this->assertCount(1, $products);
        $this->assertEquals('Orange', $products[0]['name']);
        $this->assertEquals(0, $products[0]['discount']);
    }

    public function testCreateNew()
    {
        $response = $this->post('/api/products',
            ['name' => 'Banana', 'price' => 99.99]);
        $response->assertStatus(201)
            ->assertJson([
                'name' => 'Banana',
                'price' => 99.99,
                'discount' => 0
            ]);
    }

    /**
     * @dataProvider productAttributesDataProvider
     * @param $body
     * @param $errors
     * @throws mixed
     */
    public function testCreationErrors($body, $errors)
    {
        $response = $this->post('/api/products', $body);
        $response->assertStatus(400)
            ->assertJson($errors);
    }

    public function productAttributesDataProvider()
    {
        return [
            [
                [],
                [
                    'name' => ['The name field is required.'],
                    'price' => ['The price field is required.'],
                ]
            ],

            [
                ['name' => 'Banana', 'price' => '100$'],
                [
                    'price' => ['The price format is invalid.'],
                ]
            ]
        ];
    }

    public function testUniquenessError()
    {
        factory(Product::class)->create(['name' => 'Orange']);

        $response = $this->post(
            '/api/products',
            ['name' => 'Orange', 'price' => 25]
        );

        $response->assertStatus(400)
            ->assertJson([
                "name" => [
                    "The name has already been taken."
                ]
            ]);
    }
}
