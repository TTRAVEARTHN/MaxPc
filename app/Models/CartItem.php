<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    // Mass assignable
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    // Casts
    protected $casts = [
        'quantity' => 'integer',
    ];

    // Relations
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
