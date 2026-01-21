<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    // vztah na usera
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // vztah na produkt
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
