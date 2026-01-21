<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    // pretypovanie quantity na integer pri nacitani
    protected $casts = [
        'quantity' => 'integer',
    ];

    // vztah na kosik
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // vztah na produkt
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
