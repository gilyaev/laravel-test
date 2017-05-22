<?php

namespace App\Http\Controllers;

use Validator;
use App\Voucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'count' => Voucher::count(),
            'items' => Voucher::all()
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
            'start_date' => 'required|date_format:Y-m-d|before_or_equal:end_date',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'discount' => [
                'required',
                Rule::in([
                    Voucher::DISCOUNT_10,
                    Voucher::DISCOUNT_15,
                    Voucher::DISCOUNT_20,
                    Voucher::DISCOUNT_25
                ]),
            ]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        return response()->json(
            Voucher::create($request->all()),
            201
        );
    }
}
