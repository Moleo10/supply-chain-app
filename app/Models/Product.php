<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'sku',
        'category',
        'image_url',
        'stock_quantity',
        'price',
        'sold_count',
        'shipping_method',
    ];

    protected $casts = [
        'shipping_method' => 'array',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // تم تغيير الاسم من supplier إلى user ليتطابق مع الكود في الـ View
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}