<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($testimonial) {
            if ($testimonial->isDirty('photo')) {
                Storage::delete($testimonial->getOriginal('photo'));
            }
        });

        static::deleting(function ($testimonial) {
            Storage::delete($testimonial->photo);
        });
    }
}
