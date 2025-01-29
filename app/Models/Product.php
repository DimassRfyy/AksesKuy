<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'thumbnail',
        'photo',
        'about',
        'tagline',
        'price',
        'duration',
        'capacity',
        'is_popular',
        'price_per_person',
    ];

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => [
                'name' => $value,
                'slug' => Str::slug($value)
            ]
        );
    }
    public function groups(): HasMany
    {
        return $this->hasMany(SubscriptionGroup::class);
    }

    public function keypoints(): HasMany
    {
        return $this->hasMany(ProductKeypoint::class);
    }

    public function howItWorks(): HasMany
    {
        return $this->hasMany(HowItWorks::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(ProductTestimonial::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($product) {
            if ($product->isDirty('thumbnail')) {
                Storage::delete($product->getOriginal('thumbnail'));
            }

            if ($product->isDirty('photo')) {
                Storage::delete($product->getOriginal('photo'));
            }
        });

        static::deleting(function ($product) {
            Storage::delete($product->thumbnail);
            Storage::delete($product->photo);
        });
    }
}
