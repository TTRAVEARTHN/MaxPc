<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Mass assignable
    protected $fillable = [
        'name',
        'description',
    ];

    // Relations
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
