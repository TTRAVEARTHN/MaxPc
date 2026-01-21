<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $fillable = [
        'user_id',
        'total_price',
        'status',
    ];

    // pretypovanie hodnot pre citatelnost a pracu v kode
    protected $casts = [
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // vztah na usera
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // vztah na polozky objednavky
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
