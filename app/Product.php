<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'price',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['pivot', 'vouchers', 'created_at', 'updated_at'];

    /** The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['discount'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'double',
    ];

    public function vouchers()
    {
        return $this->belongsToMany('App\Voucher')
            ->withTimestamps();
    }

    /**
     * Get the administrator flag for the user.
     *
     * @return bool
     */
    public function getDiscountAttribute()
    {
        if (isset($this->attributes['discount'])) {
            return $this->attributes['discount'];
        }
        return (int)$this->vouchers()->sum('discount');
    }

    /**
     * Get the administrator flag for the user.
     *
     * @return bool
     */
    public function setDiscountAttribute($val)
    {
        return (int)$this->attributes['discount'] = $val;
    }
}
