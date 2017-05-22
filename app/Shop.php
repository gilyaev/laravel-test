<?php

namespace App;

use App\Product;
use Illuminate\Database\Eloquent\Collection;

class Shop
{
    const MAX_DISCOUNT = 60;

    /**
     * @param \Illuminate\Database\Eloquent\Collection $products
     * @return \Illuminate\Support\Collection
     */
    public function sanitizedProductsDiscount(Collection $products)
    {
        return $products->map(function (Product $product) {
            if ($product->discount > self::MAX_DISCOUNT) {
                $product->discount = self::MAX_DISCOUNT;
            }
            return $product;
        });
    }
}