<?php

namespace App;

use App\Scopes\OutdatedScope;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    const DISCOUNT_10 = '10';
    const DISCOUNT_15 = '15';
    const DISCOUNT_20 = '20';
    const DISCOUNT_25 = '25';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start_date',
        'end_date',
        'discount',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['pivot', 'created_at', 'updated_at'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OutdatedScope);
    }
}
