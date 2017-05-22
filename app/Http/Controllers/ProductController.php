<?php

namespace App\Http\Controllers;

use Validator;
use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected static $sortableFields = ['name', 'price'];

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $builder = Product::with('vouchers');
        $sorting = $this->getSortingRules($request);
        foreach ($sorting as $field => $direction) {
            $builder->orderBy($field, $direction);
        }

        $products = $builder->get();
        return response()->json([
            'count' => Product::count(),
            'items' => app('shop')->sanitizedProductsDiscount($products)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:products|max:255',
            'price' => "required|regex:/^\d*(\.\d{1,2})?$/"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        return response()->json(
            Product::create($request->all()),
            201
        );
    }

    public function buy(Request $request, Product $product)
    {
        $product->vouchers()->delete();
        $product->delete();
        return response('', 200);
    }

    protected function getSortingRules(Request $request)
    {
        $rules = [];
        if (!$request->has('sort')) {
            return $rules;
        }

        $rawSorting =explode(",", $request->get('sort'));
        $fields = implode('|', static::$sortableFields);
        foreach ($rawSorting as $raw) {
            preg_match("/^([-|+])?($fields+)$/", trim($raw), $matches);
            if (empty($matches)) {
                continue;
            }
            $rules[$matches[2]] = $matches[1] === '-' ? 'DESC' : 'ASC';
        }

        return $rules;
    }
}
