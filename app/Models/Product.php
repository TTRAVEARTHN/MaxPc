<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'specs',
        'main_image',
    ];

    // automaticke pretypovanie (JSON, decimal, datetime)
    protected $casts = [
        'specs' => 'array',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // vztah na kategoriu
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

//    public function images()
//    {
//        return $this->hasMany(ProductImage::class);
//    }

    // vztah na oblubene polozky
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    // vztah na kosikove polozky
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // vztah na polozky objednavky
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
