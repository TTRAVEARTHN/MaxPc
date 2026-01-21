<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    // povolene mass assignment polia
    protected $fillable = [
        'user_id',
    ];

    // vztah na usera
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // vztah na polozky kosika
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
