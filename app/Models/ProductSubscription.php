<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProductSubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'proof',
        'booking_trx_id',
        'total_amount',
        'total_tax_amount',
        'customer_bank_name',
        'customer_bank_account',
        'customer_bank_number',
        'is_paid',
        'duration',
        'price',
        'product_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function group(): HasOne
    {
        return $this->hasOne(SubscriptionGroup::class, 'product_subscription_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($subscription) {
            if ($subscription->isDirty('proof')) {
                Storage::delete($subscription->getOriginal('proof'));
            }
        });

        static::deleting(function ($subscription) {
            Storage::delete($subscription->proof);
        });
    }
}
