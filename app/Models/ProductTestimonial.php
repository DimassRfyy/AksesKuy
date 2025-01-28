<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTestimonial extends Model
{

    use SoftDeletes;
    protected $fillable = [
        'product_id',
        'name',
        'rating',
        'message',
        'is_publish',
        'customer_booking_trx_id',
        'photo',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
