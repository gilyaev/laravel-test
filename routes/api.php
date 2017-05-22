<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('products/{product}/buy', 'ProductController@buy');

Route::resource('products', 'ProductController', [
    'only' => [
        'index',
        'store'
    ]
]);

Route::resource('vouchers', 'VoucherController', [
    'only' => [
        'index',
        'store'
    ]
]);

Route::resource('products.vouchers', 'Product\VoucherController', [
    'only' => [
        'index',
        'store',
        'destroy'
    ]
]);

