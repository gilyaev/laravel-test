<?php

namespace Tests\Feature;

use App\Product;
use App\Voucher;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProductVoucherTest extends TestCase
{
    use DatabaseMigrations;


    public function testProductVouchers()
    {
        $product = factory(Product::class)->create();
        $product->vouchers()->attach(factory(Voucher::class,5)->create([
            'start_date' => date('Y-m-d', time() + 3600),
            'start_date' => date('Y-m-d', time() + 7200),
            'discount' => 10
        ]));

        $product->vouchers()->attach(factory(Voucher::class)->create([
            'start_date' => date('Y-m-d', time() - 5 * (24 * 3600)),
            'end_date' => date('Y-m-d', time() - 4 * (24 * 3600)),
            'discount' => 25
        ]));

        $response = $this->get("/api/products/{$product->id}/vouchers");
        $response->assertStatus(200)
            ->assertJson([
                'count' => 5
            ]);
    }

    public function testAttach()
    {
        $product = factory(Product::class)->create();
        $voucher = factory(Voucher::class)->create();
        $response = $this->post("/api/products/{$product->id}/vouchers",
            ['voucher_id' => $voucher->id]);

        $response->assertStatus(201);
    }

    public function testAttachNotActual()
    {
        $product = factory(Product::class)->create();
        $voucher = factory(Voucher::class)->create([
            'start_date' => '2016-01-01',
            'end_date' => '2016-02-01',
        ]);
        $response = $this->post("/api/products/{$product->id}/vouchers",
            ['voucher_id' => $voucher->id]);
        $response->assertStatus(404)
            ->assertJson(["error" => "Resource not found"]);
    }

    public function testAttachToEmpty()
    {
        $voucher = factory(Voucher::class)->create([
            'start_date' => '2016-01-01',
            'end_date' => '2016-02-01',
        ]);
        $response = $this->post("/api/products/777/vouchers",
            ['voucher_id' => $voucher->id]);
        $response->assertStatus(404)
            ->assertJson(["error" => "Resource not found"]);
    }

    public function testAttachBadRequest()
    {
        $product = factory(Product::class)->create();
        $response = $this->post("/api/products/{$product->id}/vouchers",
            ['voucher_id' => 'w1']);
        
        $response->assertStatus(400)
            ->assertJson([
                "voucher_id" => [
                    "The voucher id must be an integer."
                ]
            ]);
    }


    public function testDeattach()
    {
        $product = factory(Product::class)->create();
        $voucher = factory(Voucher::class)->create();
        $response = $this->delete("/api/products/{$product->id}/vouchers/{$voucher->id}");
        $response->assertStatus(204);
    }

    public function testDeattachToEmpty()
    {
        $voucher = factory(Voucher::class)->create([
            'start_date' => '2016-01-01',
            'end_date' => '2016-02-01',
        ]);
        $response = $this->delete("/api/products/7777/vouchers/{$voucher->id}");
        $response->assertStatus(404)
            ->assertJson(["error" => "Resource not found"]);
    }

    public function testDeattachVoucherNotFound()
    {
        $product = factory(Product::class)->create();
        $response = $this->delete("/api/products/{$product->id}/vouchers/w1");
        $response->assertStatus(404)
            ->assertJson(["error" => "Resource not found"]);
    }   
}