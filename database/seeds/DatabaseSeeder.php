<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vouchers = factory(App\Voucher::class, 4)->create();
        factory(App\Product::class, 15)->create()->each(function ($product) use ($vouchers) {
            $product->vouchers()->attach($vouchers);
            $product->vouchers()->attach(factory(App\Voucher::class)->create());
        });
    }
}
