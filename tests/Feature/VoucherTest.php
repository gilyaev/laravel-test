<?php

namespace Tests\Feature;

use App\Voucher;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class VoucherTest extends TestCase
{
    use DatabaseMigrations;

    public function testIsAvailable()
    {
        $response = $this->get('/api/vouchers');
        $response->assertSuccessful();
    }

    public function testStructure()
    {
        factory(Voucher::class, 10)->create();
        $response = $this->get('/api/vouchers');
        $response->assertJsonStructure([
            'count',
            'items' => ['*' => ['id', 'start_date', 'end_date', 'discount']]
        ]);
    }

    public function testOnlyActual()
    {
        factory(Voucher::class)->create([
            'discount' => 20,
            'start_date' => '2010-12-01',
            'end_date' => '2010-12-02',
        ]);

        factory(Voucher::class)->create([
            'discount' => 15,
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
        ]);

        $response = $this->get('/api/vouchers');
        $response->assertJson([
            'count' => 1,
            'items' => [
                [
                    'discount' => 15,
                    'start_date' => date('Y-m-d'),
                    'end_date' => date('Y-m-d'),
                ]
            ]
        ]);
    }

    /**
     * @dataProvider voucherAttributesDataProvider
     * @param $body
     * @param $errors
     */
    public function testCreationErrors($body, $errors)
    {
        $response = $this->post('/api/vouchers', $body);
        $response->assertStatus(400)
            ->assertJson($errors);
    }

    public function voucherAttributesDataProvider()
    {
        return [
            [
                [],
                [
                    'start_date' => ['The start date field is required.'],
                    'end_date' => ['The end date field is required.'],
                    'discount' => ['The discount field is required.'],
                ]
            ],

            [
                [
                    'start_date' => '2017-01-22',
                    'end_date' => '2017-01-12',
                    'discount' => 15
                ],
                [
                    "start_date" => [
                        "The start date must be a date before or equal to end date."
                    ],
                    "end_date" => [
                        "The end date must be a date after or equal to today."
                    ],
                ]
            ],

            [
                [
                    'start_date' => date('Y-m-d'),
                    'end_date' => date('Y-m-d', time() + 3600),
                    'discount' => 5
                ],
                [
                    "discount" => [
                        "The selected discount is invalid."
                    ]

                ]
            ]
        ];
    }

    public function testCreateNew()
    {
        $response = $this->post('/api/vouchers', [
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', time() + 3600),
            'discount' => 25
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', time() + 3600),
                'discount' => 25
            ]);
    }
}
