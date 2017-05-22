<?php

namespace App\Http\Controllers\Product;

use Validator;

use App\Product;
use App\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Product $product)
    {
        return response()->json([
            'count' => $product->vouchers()->count(),
            'items' => $product->vouchers()->get()
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'voucher_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        
        $voucher = Voucher::findOrFail($request->input('voucher_id'));
        $product->vouchers()->attach($voucher);
        return response('', 201);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Product $product
     * @param \App\Voucher $voucher
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(
        Request $request,
        Product $product,
        Voucher $voucher
    ) {
        $product->vouchers()->detach($voucher);
        return response('', 204);
    }
}
